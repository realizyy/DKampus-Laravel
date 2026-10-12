<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\EmailOtp;
use App\Models\User;
use App\Services\OtpResult;
use App\Services\OtpService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Throwable;

/**
 * Halaman "Verifikasi Akun" (Figma): masukkan kode 6 digit yang dikirim ke email.
 * Dipakai untuk akun baru (purpose register) dan atur ulang kata sandi (purpose reset).
 * Email dan tujuan disimpan di session, bukan di URL.
 */
class OtpVerificationController extends Controller
{
    public function __construct(private OtpService $otp)
    {
    }

    public function show(Request $request): View|RedirectResponse
    {
        [$email, $purpose] = $this->context($request);
        if (! $email) {
            return redirect()->route('login');
        }

        return view('auth.otp-verify', [
            'Title' => 'Verifikasi Akun',
            'email' => $email,
            'purpose' => $purpose,
            'resendIn' => $this->otp->resendIn($email, $purpose),
        ]);
    }

    public function verify(Request $request): RedirectResponse
    {
        [$email, $purpose] = $this->context($request);
        if (! $email) {
            return redirect()->route('login');
        }

        $request->validate(['code' => ['required', 'digits:6']], [
            'code.required' => 'Masukkan kode 6 digit.',
            'code.digits' => 'Kode terdiri dari 6 digit angka.',
        ]);

        $result = $this->otp->verify($email, $purpose, (string) $request->input('code'));
        if ($result !== OtpResult::Ok) {
            return back()->withErrors(['code' => $result->message()]);
        }

        $user = User::where('email', $email)->first();
        $request->session()->forget(['otp.email', 'otp.purpose']);

        if ($purpose === EmailOtp::PURPOSE_RESET) {
            if (! $user) {
                return redirect()->route('reset.request');
            }
            // Izin singkat untuk mengganti kata sandi akun ini saja
            $request->session()->put('reset.user_id', $user->id);
            $request->session()->put('reset.expires_at', now()->addMinutes(15)->timestamp);

            return redirect()->route('reset.new');
        }

        $user?->forceFill(['email_verified_at' => now()])->save();

        return redirect()->route('account.created')->with('account_created', true);
    }

    public function resend(Request $request): RedirectResponse
    {
        [$email, $purpose] = $this->context($request);
        if (! $email) {
            return redirect()->route('login');
        }

        $wait = $this->otp->resendIn($email, $purpose);
        if ($wait > 0) {
            return back()->withErrors(['code' => "Tunggu {$wait} detik sebelum meminta kode baru."]);
        }

        // Untuk reset, email yang tidak terdaftar diperlakukan sama (tidak membocorkan keberadaan akun)
        $exists = User::where('email', $email)->exists();
        if ($purpose === EmailOtp::PURPOSE_REGISTER || $exists) {
            try {
                $this->otp->issue($email, $purpose);
            } catch (Throwable $e) {
                return back()->withErrors(['code' => 'Kode belum bisa dikirim. Coba lagi beberapa saat.']);
            }
        }

        return back()->with('status', 'Kode baru telah dikirim ke email kamu.');
    }

    /** @return array{0: ?string, 1: string} */
    private function context(Request $request): array
    {
        $email = $request->session()->get('otp.email');
        $purpose = $request->session()->get('otp.purpose', EmailOtp::PURPOSE_REGISTER);

        return [is_string($email) ? $email : null, $purpose === EmailOtp::PURPOSE_RESET ? EmailOtp::PURPOSE_RESET : EmailOtp::PURPOSE_REGISTER];
    }
}

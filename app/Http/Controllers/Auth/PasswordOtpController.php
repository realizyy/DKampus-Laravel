<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\EmailOtp;
use App\Models\User;
use App\Services\OtpService;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;
use Throwable;

/**
 * Atur ulang kata sandi lewat kode OTP (Figma): email -> kode -> kata sandi baru -> berhasil.
 * Alur tautan email bawaan Breeze (/forgot-password) tetap ada tetapi tidak lagi ditautkan dari UI.
 */
class PasswordOtpController extends Controller
{
    public function create(): View
    {
        return view('auth.reset-request', ['Title' => 'Atur Ulang Kata Sandi']);
    }

    public function store(Request $request, OtpService $otp): RedirectResponse
    {
        $request->validate(['email' => ['required', 'string', 'email']], [
            'email.required' => 'Masukkan alamat email.',
            'email.email' => 'Format email belum benar.',
        ]);

        $email = OtpService::normalize((string) $request->input('email'));

        // Respons sama untuk email terdaftar maupun tidak (tidak membocorkan keberadaan akun)
        if (User::where('email', $email)->exists()) {
            try {
                $otp->issue($email, EmailOtp::PURPOSE_RESET);
            } catch (Throwable $e) {
                return back()->withInput()->withErrors(['email' => 'Kode belum bisa dikirim. Coba lagi beberapa saat.']);
            }
        }

        $request->session()->put('otp.email', $email);
        $request->session()->put('otp.purpose', EmailOtp::PURPOSE_RESET);

        return redirect()->route('otp.show')->with('status', 'Jika email terdaftar, kode telah dikirim.');
    }

    public function edit(Request $request): View|RedirectResponse
    {
        if (! $this->allowed($request)) {
            return redirect()->route('reset.request');
        }

        return view('auth.reset-new', ['Title' => 'Kata Sandi Baru']);
    }

    public function update(Request $request): RedirectResponse
    {
        if (! $this->allowed($request)) {
            return redirect()->route('reset.request');
        }

        $request->validate([
            'password' => ['required', 'confirmed', Password::defaults()],
        ], [
            'password.required' => 'Masukkan kata sandi baru.',
            'password.confirmed' => 'Konfirmasi kata sandi tidak sama.',
        ]);

        $user = User::findOrFail($request->session()->get('reset.user_id'));
        $user->forceFill([
            'password' => Hash::make((string) $request->input('password')),
            'remember_token' => Str::random(60),
        ])->save();

        event(new PasswordReset($user));
        $request->session()->forget(['reset.user_id', 'reset.expires_at']);

        return redirect()->route('reset.done')->with('password_reset', true);
    }

    private function allowed(Request $request): bool
    {
        return $request->session()->has('reset.user_id') && (int) $request->session()->get('reset.expires_at', 0) > now()->timestamp;
    }
}

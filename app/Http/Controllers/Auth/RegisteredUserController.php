<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\EmailOtp;
use App\Models\User;
use App\Services\OtpService;
use App\Providers\RouteServiceProvider;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
// use Kreait\Laravel\Firebase\Facades\Firebase;

class RegisteredUserController extends Controller
{

    // private $firebaseauth;
    public function __construct()
    {
        // $this->firebaseauth = Firebase::auth();
    }
    /**
     * Display the registration view.
     */
    public function create()
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request, OtpService $otp): RedirectResponse
    {
        // Validasi di luar try/catch supaya pesan error tampil per field di form
        $request->validate([
            'nama' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:' . User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'no_telp' => ['required', 'string', 'max:13', 'unique:' . User::class],
        ], [], [
            'nama' => 'nama',
            'no_telp' => 'nomor telepon',
            'password' => 'kata sandi',
        ]);

        try {
            $user = DB::transaction(fn () => User::create([
                'nama_user' => $request->nama,
                'email' => $request->email,
                'no_telp' => $request->no_telp,
                'password' => Hash::make($request->password),
                'role' => "customer"
            ]));
        } catch (\Exception $e) {
            return redirect()->back()->withInput($request->except('password', 'password_confirmation'))->with('error', $e->getMessage());
        }

        event(new Registered($user));

        // Verifikasi email lewat kode OTP (Figma): akun baru ditandai menunggu verifikasi
        $request->session()->put('otp.email', $user->email);
        $request->session()->put('otp.purpose', EmailOtp::PURPOSE_REGISTER);
        try {
            $otp->issue($user->email, EmailOtp::PURPOSE_REGISTER);
        } catch (\Throwable $e) {
            return redirect()->route('otp.show')->withErrors(['code' => 'Kode belum bisa dikirim. Tekan "Kirim Ulang Kode".']);
        }

        return redirect()->route('otp.show');
    }
}

<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/** Halaman statis: onboarding (Splash + Selamat Datang), layar sukses auth, dan halaman Seputar Dkampus. */
class PageController extends Controller
{
    public function onboarding(): View
    {
        return view('pages.Users.Onboarding', ['Title' => 'Selamat Datang']);
    }

    public function accountCreated(Request $request): View|RedirectResponse
    {
        return $request->session()->has('account_created')
            ? view('auth.success', [
                'Title' => 'Akun Berhasil Dibuat',
                'heading' => 'Akun Berhasil Dibuat',
                'message' => 'Selamat! Anda telah berhasil membuat akun baru. Ingat, jangan bagikan informasi akun ini kepada siapa pun, ya!',
                'cta' => 'Masuk',
            ])
            : redirect()->route('login');
    }

    public function passwordReset(Request $request): View|RedirectResponse
    {
        return $request->session()->has('password_reset')
            ? view('auth.success', [
                'Title' => 'Kata Sandi Berhasil Diubah',
                'heading' => 'Kata Sandi Berhasil Diubah',
                'message' => 'Selamat! Perubahan kata sandi Anda telah berhasil dilakukan. Ingat, jangan bagikan kata sandi ini ke siapapun, ya!',
                'cta' => 'Masuk',
            ])
            : redirect()->route('login');
    }

    public function about(): View
    {
        return view('pages.Users.About', ['Title' => 'Tentang Kami']);
    }

    public function terms(): View
    {
        return view('pages.Users.Terms', ['Title' => 'Syarat dan Ketentuan']);
    }

    public function privacy(): View
    {
        return view('pages.Users.Privacy', ['Title' => 'Kebijakan Privasi']);
    }
}

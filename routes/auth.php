<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\ConfirmablePasswordController;
use App\Http\Controllers\Auth\EmailVerificationNotificationController;
use App\Http\Controllers\Auth\EmailVerificationPromptController;
use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Controllers\Auth\OtpVerificationController;
use App\Http\Controllers\Auth\PasswordOtpController;
use App\Http\Controllers\Auth\PasswordController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Auth\VerifyEmailController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function () {
    Route::get('register', [UserController::class, 'register'])
        ->name('register');

    Route::post('register', [RegisteredUserController::class, 'store']);

    Route::get('login', [UserController::class, 'login'])
        ->name('login');

    Route::post('login', [AuthenticatedSessionController::class, 'store']);

    Route::get('forgot-password', [PasswordResetLinkController::class, 'create'])
        ->name('password.request');

    Route::post('forgot-password', [PasswordResetLinkController::class, 'store'])
        ->name('password.email');

    Route::get('reset-password/{token}', [NewPasswordController::class, 'create'])
        ->name('password.reset');

    Route::post('reset-password', [NewPasswordController::class, 'store'])
        ->name('password.store');

    // Verifikasi akun & atur ulang kata sandi lewat kode OTP email (Figma)
    Route::get('verifikasi-akun', [OtpVerificationController::class, 'show'])->name('otp.show');
    Route::post('verifikasi-akun', [OtpVerificationController::class, 'verify'])->middleware('throttle:10,1')->name('otp.verify');
    Route::post('verifikasi-akun/kirim-ulang', [OtpVerificationController::class, 'resend'])->middleware('throttle:6,1')->name('otp.resend');

    Route::get('atur-ulang-kata-sandi', [PasswordOtpController::class, 'create'])->name('reset.request');
    Route::post('atur-ulang-kata-sandi', [PasswordOtpController::class, 'store'])->middleware('throttle:5,1')->name('reset.send');
    Route::get('kata-sandi-baru', [PasswordOtpController::class, 'edit'])->name('reset.new');
    Route::post('kata-sandi-baru', [PasswordOtpController::class, 'update'])->middleware('throttle:6,1')->name('reset.update');
});

Route::middleware('auth')->group(function () {
    Route::get('verify-email', EmailVerificationPromptController::class)
        ->name('verification.notice');

    Route::get('verify-email/{id}/{hash}', VerifyEmailController::class)
        ->middleware(['signed', 'throttle:6,1'])
        ->name('verification.verify');

    Route::post('email/verification-notification', [EmailVerificationNotificationController::class, 'store'])
        ->middleware('throttle:6,1')
        ->name('verification.send');

    Route::get('confirm-password', [ConfirmablePasswordController::class, 'show'])
        ->name('password.confirm');

    Route::post('confirm-password', [ConfirmablePasswordController::class, 'store']);

    Route::put('password', [PasswordController::class, 'update'])->name('password.update');

    Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])
        ->name('logout');
});

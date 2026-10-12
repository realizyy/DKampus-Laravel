<?php

namespace App\Services;

use App\Mail\OtpCodeMail;
use App\Models\EmailOtp;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Throwable;

/**
 * OTP 6 digit lewat email untuk verifikasi akun dan atur ulang kata sandi.
 * Hanya HMAC kode yang disimpan; kode berlaku 10 menit, maksimal 5 percobaan, dan boleh dikirim ulang tiap 60 detik.
 */
class OtpService
{
    public const TTL_MINUTES = 10;

    public const RESEND_SECONDS = 60;

    public const MAX_ATTEMPTS = 5;

    public static function normalize(string $email): string
    {
        return mb_strtolower(trim($email));
    }

    /** Buat OTP baru (menggantikan yang lama) dan kirim lewat email. Mengembalikan kode (berguna untuk test). */
    public function issue(string $email, string $purpose): string
    {
        $email = self::normalize($email);
        $code = str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        EmailOtp::updateOrCreate(
            ['email' => $email, 'purpose' => $purpose],
            [
                'code_hash' => $this->hash($code),
                'attempts' => 0,
                'expires_at' => now()->addMinutes(self::TTL_MINUTES),
                'sent_at' => now(),
            ]
        );

        $this->deliver($email, $code, $purpose);

        return $code;
    }

    /** Detik tersisa sebelum kode boleh dikirim ulang (0 = boleh). */
    public function resendIn(string $email, string $purpose): int
    {
        $otp = EmailOtp::where('email', self::normalize($email))->where('purpose', $purpose)->first();
        if (! $otp) {
            return 0;
        }

        return max(0, self::RESEND_SECONDS - $otp->sent_at->diffInSeconds(now()));
    }

    public function verify(string $email, string $purpose, string $code): OtpResult
    {
        $otp = EmailOtp::where('email', self::normalize($email))->where('purpose', $purpose)->first();
        if (! $otp) {
            return OtpResult::Invalid;
        }
        if ($otp->expires_at->isPast()) {
            return OtpResult::Expired;
        }
        if ($otp->attempts >= self::MAX_ATTEMPTS) {
            return OtpResult::TooManyAttempts;
        }

        if (! hash_equals($otp->code_hash, $this->hash(trim($code)))) {
            $otp->increment('attempts');

            return $otp->attempts >= self::MAX_ATTEMPTS ? OtpResult::TooManyAttempts : OtpResult::Invalid;
        }

        $otp->delete(); // sekali pakai

        return OtpResult::Ok;
    }

    /** Ada OTP verifikasi akun yang belum diselesaikan (akun baru yang belum verifikasi email)? */
    public function hasPendingRegistration(string $email): bool
    {
        return EmailOtp::where('email', self::normalize($email))->where('purpose', EmailOtp::PURPOSE_REGISTER)->exists();
    }

    private function hash(string $code): string
    {
        return hash_hmac('sha256', $code, (string) config('app.key'));
    }

    private function deliver(string $email, string $code, string $purpose): void
    {
        try {
            Mail::to($email)->send(new OtpCodeMail($code, $purpose, self::TTL_MINUTES));
        } catch (Throwable $e) {
            report($e);
            // Di lingkungan lokal tanpa server SMTP, kode dicatat di log supaya alur tetap bisa dicoba
            if (app()->isLocal()) {
                Log::warning("OTP {$purpose} untuk {$email}: {$code}");
            } else {
                throw $e;
            }
        }
    }
}

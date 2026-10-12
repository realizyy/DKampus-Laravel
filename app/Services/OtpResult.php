<?php

namespace App\Services;

enum OtpResult: string
{
    case Ok = 'ok';
    case Invalid = 'invalid';
    case Expired = 'expired';
    case TooManyAttempts = 'too_many_attempts';

    public function message(): string
    {
        return match ($this) {
            self::Ok => '',
            self::Invalid => 'Kode yang kamu masukkan salah.',
            self::Expired => 'Kode sudah kedaluwarsa. Kirim ulang kode untuk mendapatkan yang baru.',
            self::TooManyAttempts => 'Terlalu banyak percobaan. Kirim ulang kode untuk mendapatkan yang baru.',
        };
    }
}

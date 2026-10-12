<?php

namespace App\Mail;

use App\Models\EmailOtp;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

class OtpCodeMail extends Mailable
{
    public function __construct(public string $code, public string $purpose, public int $ttlMinutes)
    {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: $this->purpose === EmailOtp::PURPOSE_RESET ? 'Kode atur ulang kata sandi DKampus' : 'Kode verifikasi akun DKampus',
        );
    }

    public function content(): Content
    {
        return new Content(view: 'emails.otp');
    }
}

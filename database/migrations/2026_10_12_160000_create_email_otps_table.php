<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Kode OTP email untuk verifikasi akun baru (purpose "register") dan atur ulang kata sandi (purpose "reset").
 * Kode TIDAK disimpan polos: hanya HMAC-nya (lihat App\Services\OtpService).
 */
return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('email_otps')) {
            return;
        }

        Schema::create('email_otps', function (Blueprint $table) {
            $table->id();
            $table->string('email')->index();
            $table->string('purpose', 20);
            $table->string('code_hash', 64);
            $table->unsignedTinyInteger('attempts')->default(0);
            $table->timestamp('expires_at');
            $table->timestamp('sent_at');
            $table->timestamps();

            // Satu OTP aktif per email dan tujuan; kirim ulang menggantikan baris lama
            $table->unique(['email', 'purpose']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('email_otps');
    }
};

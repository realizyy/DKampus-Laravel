<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Kolom users.restriction (pemblokiran akun oleh admin) dipakai kode (AdminController::editUser,
 * middleware role) dan ada di database nyata, tetapi tidak pernah ada di migration sehingga
 * `migrate:fresh` menghasilkan tabel tanpa kolom ini. Dilewati kalau kolomnya sudah ada.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasColumn('users', 'restriction')) {
            return;
        }

        Schema::table('users', function (Blueprint $table) {
            $table->boolean('restriction')->default(false)->after('no_telp');
        });
    }

    public function down(): void
    {
        if (Schema::hasColumn('users', 'restriction')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('restriction');
            });
        }
    }
};

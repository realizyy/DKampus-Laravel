<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('data_umkms', function (Blueprint $table) {
            if (!Schema::hasColumn('data_umkms', 'user_id')) {
                $table->unsignedBigInteger('user_id')->nullable()->after('id');
            }
            if (!Schema::hasColumn('data_umkms', 'open_time')) {
                $table->dateTime('open_time')->nullable();
            }
            if (!Schema::hasColumn('data_umkms', 'close_time')) {
                $table->dateTime('close_time')->nullable();
            }
            if (!Schema::hasColumn('data_umkms', 'link')) {
                $table->text('link')->nullable();
            }
            if (!Schema::hasColumn('data_umkms', 'geo')) {
                $table->string('geo')->nullable();
            }
        });

        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'fcm_token')) {
                $table->text('fcm_token')->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('data_umkms', function (Blueprint $table) {
            $table->dropColumn(['user_id', 'open_time', 'close_time', 'link', 'geo']);
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('fcm_token');
        });
    }
};

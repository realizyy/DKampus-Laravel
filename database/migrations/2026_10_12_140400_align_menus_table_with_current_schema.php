<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration awal `menus` (2023_10_26_123216) memakai kolom `promo` (int) dan `kategori` (string),
 * sedangkan database yang dipakai aplikasi (dan seluruh kode) memakai `diskon` (float) dan
 * `category` (enum). Migration ini menyamakan hasil `migrate:fresh` dengan skema itu.
 * Di database yang sudah benar (mis. dari dump) semua langkah di bawah dilewati.
 */
return new class extends Migration
{
    private const CATEGORIES = [
        'minuman', 'jajanan', 'aneka nasi', 'ayam & bebek', 'cepat saji',
        'roti', 'bakso & soto', 'bakmie', 'mie', 'sate',
    ];

    public function up(): void
    {
        Schema::table('menus', function (Blueprint $table) {
            if (Schema::hasColumn('menus', 'promo')) {
                $table->dropColumn('promo');
            }
            if (Schema::hasColumn('menus', 'kategori')) {
                $table->dropColumn('kategori');
            }
        });

        Schema::table('menus', function (Blueprint $table) {
            if (! Schema::hasColumn('menus', 'diskon')) {
                $table->float('diskon')->nullable()->default(0)->after('harga');
            }
            if (! Schema::hasColumn('menus', 'category')) {
                $table->enum('category', self::CATEGORIES)->nullable()->after('rating');
            }
        });
    }

    public function down(): void
    {
        // Tidak ada rollback: kolom lama (promo/kategori) tidak dipakai lagi oleh aplikasi.
    }
};

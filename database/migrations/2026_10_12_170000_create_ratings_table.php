<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/** Rating dan ulasan pembeli untuk satu pesanan selesai (Figma: Rating Pelayanan, Rating Makanan, aspek, ulasan). */
return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('ratings')) {
            return;
        }

        Schema::create('ratings', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->index();
            $table->unsignedBigInteger('history_id');
            $table->unsignedBigInteger('umkm_id')->nullable()->index();
            $table->unsignedTinyInteger('rating_pelayanan');
            $table->unsignedTinyInteger('rating_makanan');
            $table->json('aspects')->nullable();
            $table->text('review')->nullable();
            $table->timestamps();

            // Satu rating per pesanan per pembeli
            $table->unique(['user_id', 'history_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ratings');
    }
};

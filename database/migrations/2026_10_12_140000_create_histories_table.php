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
        if (Schema::hasTable('histories')) {
            return;
        }

        Schema::create('histories', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('cour_id');
            // 0 untuk order jastip (tanpa UMKM)
            $table->unsignedBigInteger('umkm_id')->default(0);
            $table->text('item');
            $table->integer('harga');
            $table->integer('ongkir');
            $table->float('jarak')->nullable();
            $table->string('status');
            $table->string('bukti')->nullable();
            $table->string('bukti_akhir')->nullable();
            $table->text('alasan')->nullable();
            $table->integer('total_driver')->nullable();
            $table->string('kategori')->nullable();
            $table->string('order_id');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('histories');
    }
};

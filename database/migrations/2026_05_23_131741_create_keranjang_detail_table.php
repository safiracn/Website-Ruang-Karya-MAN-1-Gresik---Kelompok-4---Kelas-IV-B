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
    Schema::create('keranjang_detail', function (Blueprint $table) {
        $table->id('id_keranjang_detail');
        $table->unsignedBigInteger('id_keranjang');
        $table->unsignedBigInteger('id_varian');
        $table->integer('jumlah')->default(1);
        $table->timestamps();

        $table->foreign('id_keranjang')
              ->references('id_keranjang')->on('keranjang')->onDelete('cascade');
        $table->foreign('id_varian')
              ->references('id_varian')->on('produk_varian')->onDelete('cascade');
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('keranjang_detail');
    }
};

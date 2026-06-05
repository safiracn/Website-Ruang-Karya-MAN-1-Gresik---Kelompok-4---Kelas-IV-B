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
    Schema::create('pembelian_detail', function (Blueprint $table) {
        $table->id('id_pembelian_detail');
        $table->unsignedBigInteger('id_pembelian')->nullable();
        $table->unsignedBigInteger('id_varian')->nullable();
        $table->integer('jumlah');
        $table->decimal('harga_satuan', 12, 2);
        $table->decimal('subtotal', 12, 2);
        $table->timestamps();

        $table->foreign('id_pembelian')->references('id_pembelian')->on('pembelian');
        $table->foreign('id_varian')->references('id_varian')->on('produk_varian');
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pembelian_detail');
    }
};

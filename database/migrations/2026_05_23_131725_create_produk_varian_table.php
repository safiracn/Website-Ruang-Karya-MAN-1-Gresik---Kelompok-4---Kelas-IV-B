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
    Schema::create('produk_varian', function (Blueprint $table) {
        $table->id('id_varian');
        $table->unsignedBigInteger('id_produk')->nullable();
        $table->string('nama_varian', 50)->nullable();
        $table->decimal('harga', 12, 2);
        $table->integer('stok')->default(0);
        $table->timestamps();

        $table->foreign('id_produk')
              ->references('id_produk')->on('produk')
              ->onDelete('cascade');
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('produk_varian');
    }
};

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
    Schema::create('produk', function (Blueprint $table) {
        $table->id('id_produk');
        $table->unsignedBigInteger('id_kategori')->nullable();
        $table->string('nama_produk', 150);
        $table->text('deskripsi')->nullable();
        $table->string('bahan', 100)->nullable();
        $table->string('finishing', 100)->nullable();
        $table->string('dimensi', 100)->nullable();
        $table->string('garansi', 50)->nullable();
        $table->string('foto_produk', 255)->nullable();
        $table->timestamps();

        $table->foreign('id_kategori')->references('id_kategori')->on('kategori');
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('produk');
    }
};

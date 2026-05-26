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
    Schema::create('pembelian', function (Blueprint $table) {
        $table->id('id_pembelian');
        $table->unsignedBigInteger('id_user')->nullable();
        $table->string('nama_penerima', 100);
        $table->string('no_telp_penerima', 20);
        $table->string('provinsi', 100);
        $table->string('kota_kabupaten', 100);
        $table->string('kode_pos', 10);
        $table->text('detail_alamat');
        $table->enum('metode_pengiriman', ['Ambil', 'Antar']);
        $table->enum('status_pembayaran', ['Sudah dibayar', 'Belum Dibayar'])
              ->default('Belum Dibayar');
        $table->enum('status_kirim', ['Belum dikirim', 'Dikirim', 'Diterima'])
              ->default('Belum dikirim');
        $table->enum('status_pesanan', ['Pending', 'Diproses', 'Selesai', 'Dibatalkan'])
              ->default('Pending');
        $table->decimal('total_harga', 12, 2)->default(0);
        $table->timestamps();

        $table->foreign('id_user')->references('id')->on('users')->onDelete('cascade');
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pembelian');
    }
};

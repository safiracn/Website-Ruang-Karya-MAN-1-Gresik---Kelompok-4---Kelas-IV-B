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
    Schema::create('users', function (Blueprint $table) {
        $table->bigIncrements('id');
        $table->string('nama_lengkap');
        $table->string('email')->unique();
        $table->string('password');
        $table->string('no_telp', 20)->nullable();
        $table->text('alamat')->nullable();
        
        // 1. GANTI BARIS ENUM MENJADI FOREIGN KEY INI:
        $table->foreignId('role_id')->default(2)->constrained('roles')->onDelete('cascade');
        
        $table->rememberToken();
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
    }
};

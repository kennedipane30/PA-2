<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
  public function up(): void
{
    Schema::create('users', function (Blueprint $table) {
        $table->id('user_id'); // Primary Key

        // Relasi ke tabel roles (Pastikan merujuk ke role_id)
        $table->foreignId('role_id')->constrained('roles', 'role_id')->onDelete('cascade');

        $table->string('name');
        $table->string('email')->unique();
        
        // --- TAMBAHKAN/PASTIKAN BARIS INI ADA ---
        $table->string('password'); 
        // ----------------------------------------

        $table->string('phone')->nullable();
        $table->boolean('is_verified')->default(false);
        $table->rememberToken();
        $table->timestamps();
    });
}
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};

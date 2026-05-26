<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void {
        Schema::create('enrollments', function (Blueprint $table) {
    $table->id('enrollment_id');
    
    // Relasi ke User (Siswa)
    $table->foreignId('user_id')->constrained('users', 'user_id')->onDelete('cascade');
    
    // MODIFIKASI DISINI:
    // Pastikan merujuk ke 'program_id' di tabel 'programs'
    $table->foreignId('program_id') 
          ->constrained('programs', 'program_id') // <--- Kuncinya di sini
          ->onDelete('cascade');
    
    $table->enum('status', ['active', 'completed', 'dropped'])->default('active');
    $table->timestamps();
});
    }

    public function down(): void {
        Schema::dropIfExists('enrollments');
    }
};
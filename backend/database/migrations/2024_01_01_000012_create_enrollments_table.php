<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void {
        Schema::create('enrollments', function (Blueprint $table) {
            $table->id('enrollment_id');

            // User yang mendaftar
            $table->foreignId('user_id')->constrained('users', 'user_id')->onDelete('cascade');
            
            // Program yang dipilih (Pastikan nama tabelnya 'programs' atau ganti sesuai tabel Anda)
            $table->foreignId('class_id')->constrained('programs')->onDelete('cascade');
            
            $table->string('status')->default('pending'); // pending, active, completed
            $table->boolean('status_aktif')->default(false);
            $table->decimal('progress', 5, 2)->default(0); 
            $table->timestamp('enrolled_at')->nullable();
            $table->timestamps();
            
            // Mencegah pendaftaran ganda pada kelas yang sama
            $table->unique(['user_id', 'class_id']);
        });
    }

    public function down(): void {
        Schema::dropIfExists('enrollments');
    }
};
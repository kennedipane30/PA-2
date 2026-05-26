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
        Schema::create('attendances', function (Blueprint $table) {
            $table->id('attendance_id');

            // PERBAIKAN DI SINI:
            // Pastikan merujuk ke tabel 'schedules' dan kolom 'schedule_id'
            $table->foreignId('schedule_id')
                  ->constrained('schedules', 'schedule_id') // <--- Inilah kuncinya
                  ->onDelete('cascade');
            
            // Relasi ke User/Siswa (Samakan juga ke user_id)
            $table->foreignId('user_id')
                  ->constrained('users', 'user_id')
                  ->onDelete('cascade');

            $table->date('tanggal');
            $table->enum('status', ['hadir', 'izin', 'sakit', 'alpha'])->default('alpha');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attendances');
    }
};
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
            // 1. Primary Key untuk tabel ini
            $table->id('attendance_id');

            // 2. Relasi ke Jadwal (Schedules)
            // PERBAIKAN: Gunakan 'id' sebagai referensi, karena di tabel schedules kuncinya bernama 'id'
            $table->foreignId('schedule_id')
                  ->constrained('schedules', 'id') 
                  ->onDelete('cascade');

            // 3. Relasi ke Siswa (Users)
            // Tetap gunakan 'user_id' karena kita sudah mengubahnya di tabel users
            $table->foreignId('user_id')
                  ->constrained('users', 'user_id') 
                  ->onDelete('cascade');

            $table->enum('status', ['hadir', 'izin', 'sakit', 'alfa']);
            $table->date('date');
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
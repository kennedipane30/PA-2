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
        Schema::create('schedules', function (Blueprint $table) {
            $table->id();

            // 1. Relasi ke Program Kelas (Pastikan nama tabelnya 'programs')
            $table->foreignId('class_id')
                  ->constrained('programs') 
                  ->onDelete('cascade');

            // 2. Relasi ke Pengajar (Dari tabel users)
            // PERBAIKAN: Tambahkan 'user_id' agar tidak mencari kolom 'id'
            $table->foreignId('teacher_id')
                  ->constrained('users', 'user_id') 
                  ->onDelete('cascade');

            $table->enum('hari', ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu']);
            $table->time('jam_mulai');
            $table->time('jam_selesai');
            $table->string('ruangan')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('schedules');
    }
};
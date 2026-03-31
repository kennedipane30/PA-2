<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('schedules', function (Blueprint $table) {
            $table->id('schedule_id'); // Sesuai CDM

            // PERBAIKAN 1: Hubungkan ke class_id di tabel classes (Bukan class_modelsID)
            $table->foreignId('class_id')->constrained('classes', 'class_id')->onDelete('cascade');

            // PERBAIKAN 2: Jika jadwal ini mencatat pengajar, hubungkan ke teacher_id
            $table->foreignId('teacher_id')->constrained('teachers', 'teacher_id')->onDelete('cascade');

            // Kolom Lainnya
            $table->string('title');
            $table->dateTime('start_time');
            $table->dateTime('end_time');
            $table->string('location')->nullable(); // Misal: Zoom Link atau Ruang Kelas
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('schedules');
    }
};
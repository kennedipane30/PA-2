<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('materials', function (Blueprint $table) {
<<<<<<< HEAD
            $table->id('material_id'); // Disarankan pakai nama spesifik
            
            // MODIFIKASI DISINI: 
            // 1. Ganti 'class_id' jadi 'program_id' agar konsisten
            // 2. Tambahkan ', 'program_id' di dalam constrained agar dia tahu kolom mana yang dicari
            $table->foreignId('program_id')
                  ->constrained('programs', 'program_id') 
                  ->onDelete('cascade');
            
            $table->string('judul_materi');
            $table->text('deskripsi')->nullable();
            $table->string('file_atau_link'); 
=======
            // 1. Primary Key (PK) sesuai keinginan Anda
            $table->id('materialsID');

            // 2. Relasi ke tabel programs (sebelumnya class_models)
            $table->foreignId('class_id')
                  ->constrained('programs', 'id')
                  ->onDelete('cascade');

            // 3. Judul Materi/Subjek (TIU, TWK, dll)
            $table->string('title');

            // 4. Urutan Materi (Materi ke-1, 2, dst) - WAJIB ADA untuk fitur urutan
            $table->integer('order_priority')->default(1);

            // 5. Path File PDF
            $table->string('file_path')->nullable();

>>>>>>> b33bd9ca539f5e9c5320c729d852cb06393aaa54
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('materials');
    }
};
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
        Schema::create('materials', function (Blueprint $table) {
            // Primary Key sesuai CDM (Gunakan material_id agar konsisten)
            $table->id('material_id'); 

            // PERBAIKAN 1: Hubungkan ke tabel 'classes' dan kolom 'class_id'
            // (Bukan lagi class_models)
            $table->foreignId('class_id')->constrained('classes', 'class_id')->onDelete('cascade');

            // PERBAIKAN 2: Hubungkan ke tabel 'teachers' dan kolom 'teacher_id'
            $table->foreignId('teacher_id')->constrained('teachers', 'teacher_id')->onDelete('cascade');

            $table->string('title', 50);
            
            // Enum untuk tipe (video, pdf, dll) agar lebih rapi
            $table->enum('type', ['video', 'pdf', 'link', 'document'])->default('pdf');
            
            $table->text('file_path')->nullable(); 
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('materials');
    }
};
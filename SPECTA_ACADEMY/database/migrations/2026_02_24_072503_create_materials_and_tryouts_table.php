<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Membuat Tabel Materials (Untuk Video/Materi)
        Schema::create('materials', function (Blueprint $table) {
            $table->id('materialsID'); // Primary Key sesuai ERD
            // Foreign Key merujuk ke tabel class_models
            $table->foreignId('class_id')->constrained('class_models', 'class_modelsID')->onDelete('cascade');
            $table->string('title');
            $table->timestamps();
        });

        // 2. Membuat Tabel Tryouts (Untuk Simulasi Ujian)
        Schema::create('tryouts', function (Blueprint $table) {
            $table->id('tryoutsID'); // Primary Key sesuai ERD
            $table->foreignId('class_id')->constrained('class_models', 'class_modelsID')->onDelete('cascade');
            $table->string('title');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tryouts');
        Schema::dropIfExists('materials');
    }
};

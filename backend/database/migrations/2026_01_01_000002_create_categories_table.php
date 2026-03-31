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
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Nama Kategori (Contoh: Calon Abdi Negara)
            $table->string('slug')->unique(); // Untuk URL (Contoh: calon-abdi-negara)
            
            // Kolom Tambahan untuk Specta Academy
            $table->string('icon')->nullable(); // Nama ikon atau path file ikon (FontAwesome/SVG)
            $table->text('description')->nullable(); // Deskripsi singkat kategori
            $table->boolean('is_active')->default(true); // Status aktif/tidak
            
            $table->timestamps(); // created_at & updated_at
        });
    }

    /**
     * Reverse the migrations.
     */ 
    public function down(): void
    {
        Schema::dropIfExists('categories');
    }
};
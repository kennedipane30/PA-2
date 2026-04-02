<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
   public function up(): void
{
    Schema::create('materials', function (Blueprint $table) {
        $table->id();
        
        // UBAH INI: Pastikan merujuk ke 'programs'
        $table->foreignId('class_id')->constrained('programs')->onDelete('cascade');
        
        $table->string('judul_materi');
        $table->text('deskripsi')->nullable();
        $table->string('file_atau_link'); // Link video atau file PDF
        $table->timestamps();
    });
}
    public function down(): void
    {
        Schema::dropIfExists('materials');
    }
};

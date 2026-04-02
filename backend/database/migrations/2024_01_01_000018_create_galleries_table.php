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
        Schema::create('galleries', function (Blueprint $table) {
            $table->id();

            // PERBAIKAN: Tambahkan parameter kedua 'user_id' agar tidak mencari kolom 'id'
            $table->foreignId('uploaded_by')
                  ->constrained('users', 'user_id') 
                  ->onDelete('cascade');

            $table->string('judul');
            $table->text('deskripsi')->nullable();
            $table->string('image_path');
            $table->string('kategori')->nullable();
            $table->boolean('is_published')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('galleries');
    }
};
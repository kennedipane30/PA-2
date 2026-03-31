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
        Schema::create('classes', function (Blueprint $table) {
            // Primary Key sesuai CDM
            $table->id('class_id');

            // Foreign Keys (Menghubungkan ke tabel teachers dan categories)
            $table->foreignId('teachers_id')->constrained('teachers', 'teacher_id')->onDelete('cascade');
            $table->foreignId('category_id')->constrained('categories', 'id')->onDelete('cascade');

            // Kolom sesuai CDM
            $table->string('title', 50);
            $table->text('description'); // Text di CDM
            $table->decimal('price', 10, 2); // Decimal (10)
            $table->date('start_date');
            $table->date('end_date');
            
            $table->timestamps(); // created_at & updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('classes');
    }
};
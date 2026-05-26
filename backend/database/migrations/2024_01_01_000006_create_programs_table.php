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
    Schema::create('programs', function (Blueprint $table) {
        $table->id('program_id'); // Primary Key
        $table->string('title');
        
        // PASTIKAN BARIS INI ADA:
        $table->text('description')->nullable(); 
        
        $table->decimal('price', 12, 2);
        $table->string('image')->nullable();
        
        // Relasi
        $table->foreignId('teachers_id')->nullable()->constrained('teachers', 'teacher_id')->onDelete('set null');
        $table->foreignId('category_id')->nullable()->constrained('categories', 'category_id')->onDelete('set null');
        
        $table->date('start_date')->nullable();
        $table->date('end_date')->nullable();
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('programs');
    }
};
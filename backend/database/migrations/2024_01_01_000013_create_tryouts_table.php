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
        Schema::create('tryouts', function (Blueprint $table) {
            $table->id();

            // PERBAIKAN: Ubah rujukan dari 'classes' menjadi 'programs'
            $table->foreignId('class_id')
                  ->constrained('programs') 
                  ->onDelete('cascade');

            $table->string('judul');
            $table->text('deskripsi')->nullable();
            $table->integer('durasi'); // dalam menit
            $table->integer('passing_score')->default(70);
            $table->boolean('is_active')->default(true);
            $table->timestamp('start_time')->nullable();
            $table->timestamp('end_time')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tryouts');
    }
};
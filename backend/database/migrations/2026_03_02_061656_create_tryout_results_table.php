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
        Schema::create('tryout_results', function (Blueprint $table) {
            // Primary Key untuk tabel ini
            $table->id('result_id');

            // Menghubungkan ke user_id di tabel users
            $table->foreignId('user_id')->constrained('users', 'user_id')->onDelete('cascade');

            // Menghubungkan ke tryout_id di tabel tryouts
            $table->foreignId('tryout_id')->constrained('tryouts', 'tryout_id')->onDelete('cascade');

            // Kolom Hasil Ujian
            $table->integer('score')->default(0); 
            $table->integer('correct_answers')->default(0);
            $table->integer('wrong_answers')->default(0);
            $table->integer('unanswered')->default(0);
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tryout_results');
    }
};
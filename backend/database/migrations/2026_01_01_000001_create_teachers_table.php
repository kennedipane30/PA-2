<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('teachers', function (Blueprint $table) {
            $table->id('teacher_id');

            // Pastikan relasi ke tabel users sesuai CDM
            $table->foreignId('user_id')->nullable()->constrained('users', 'user_id')->onDelete('cascade');

            $table->string('specialization', 100);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('teachers');
    }
};
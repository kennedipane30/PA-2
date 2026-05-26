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
            $table->id();

            // PERBAIKAN: Gunakan 'user_id' sebagai referensi ke tabel users
            $table->foreignId('user_id')
                  ->constrained('users', 'user_id') 
                  ->onDelete('cascade');

            // Relasi ke Tryout
            // Pastikan di migrasi Tryouts (no 13) menggunakan nama kolom yang sesuai
            $table->foreignId('tryout_id')->constrained('tryouts')->onDelete('cascade');

            $table->integer('total_soal');
            $table->integer('benar');
            $table->integer('salah');
            $table->integer('kosong');
            $table->decimal('nilai', 5, 2);
            $table->boolean('is_passed')->default(false);
            $table->timestamp('started_at')->nullable();
            $table->timestamp('submitted_at')->nullable();
            $table->integer('durasi_pengerjaan')->nullable(); // dalam detik
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
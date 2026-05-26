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
       Schema::create('schedules', function (Blueprint $table) {
    $table->id('schedule_id');
    
    // MODIFIKASI DISINI:
    // 1. Ganti 'class_id' menjadi 'program_id' agar seragam (Opsional tapi disarankan)
    // 2. Beri tahu Laravel untuk mencari kolom 'program_id' di tabel 'programs'
    $table->foreignId('program_id')
          ->constrained('programs', 'program_id') // <--- Bagian ini kuncinya
          ->onDelete('cascade');
    
    $table->string('hari');
    $table->time('jam_mulai');
    $table->time('jam_selesai');
    $table->timestamps();
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('schedules');
    }
};
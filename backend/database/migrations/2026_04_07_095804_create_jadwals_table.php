<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('jadwals', function (Blueprint $table) {
            $table->id();
            // Sesuaikan foreign key dengan tabel program (course_classes)
            $table->unsignedBigInteger('class_id');
            // Sesuaikan foreign key dengan tabel users (menggunakan user_id sesuai model Anda)
            $table->unsignedBigInteger('user_id');

            $table->string('material_title');
            $table->date('date');
            $table->time('start_time');
            $table->time('end_time');
            $table->string('status')->default('aktif');
            $table->timestamps();

            // Relasi (Opsional tapi direkomendasikan)
            $table->foreign('class_id')->references('id')->on('course_classes')->onDelete('cascade');
            $table->foreign('user_id')->references('user_id')->on('users')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('jadwals');
    }
};

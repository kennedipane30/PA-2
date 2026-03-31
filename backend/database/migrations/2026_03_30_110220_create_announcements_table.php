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
        Schema::create('announcements', function (Blueprint $table) {
            // Primary Key sesuai CDM
            $table->id('announcement_id'); 

            // PERBAIKAN: Hubungkan ke user_id di tabel users (Bukan usersID)
            $table->foreignId('user_id')->constrained('users', 'user_id')->onDelete('cascade');

            // Pastikan ini juga nyambung ke class_id di tabel classes
            $table->foreignId('class_id')->constrained('classes', 'class_id')->onDelete('cascade');

            $table->string('title', 50);
            $table->text('content'); 
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('announcements');
    }
};

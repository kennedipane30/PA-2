<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('otp_codes', function (Blueprint $table) {
            $table->id(); // ID untuk tabel OTP sendiri
            
            // Perbaikan di sini: menghubungkan user_id ke usersID di tabel users
            $table->foreignId('user_id')->constrained('users', 'usersID')->onDelete('cascade');
            
            $table->string('email')->index();
            $table->string('otp_code');
            $table->timestamp('expired_at');
            $table->boolean('is_used')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('otp_codes');
    }
};
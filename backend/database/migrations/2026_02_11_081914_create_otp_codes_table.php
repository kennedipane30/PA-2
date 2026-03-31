<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('otp_codes', function (Blueprint $table) {
            $table->id(); 
            
            // PERBAIKAN: Harus 'user_id' (huruf kecil semua, pakai underscore) 
            // agar cocok dengan Primary Key yang baru di tabel users
            $table->foreignId('user_id')->constrained('users', 'user_id')->onDelete('cascade');
            
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
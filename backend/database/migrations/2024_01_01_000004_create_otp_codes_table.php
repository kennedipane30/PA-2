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
        Schema::create('otp_codes', function (Blueprint $table) {
            $table->id(); 
            
            // MODIFIKASI: Menggunakan foreignId agar lebih aman dan ringkas
            // Parameter kedua 'user_id' memberitahu Laravel bahwa primary key di tabel users bukan 'id'
            $table->foreignId('user_id')
                  ->constrained('users', 'user_id')
                  ->onDelete('cascade');

            $table->string('email')->index();
            $table->string('otp_code');
            $table->timestamp('expired_at');
            $table->boolean('is_used')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('otp_codes');
    }
};
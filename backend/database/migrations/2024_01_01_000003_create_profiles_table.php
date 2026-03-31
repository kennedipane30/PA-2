<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('nomor_wa')->nullable(); // HP Siswa - Encrypted
            $table->text('alamat')->nullable(); // Encrypted
            $table->string('nama_ibu')->nullable(); // Encrypted
            $table->string('nomor_wa_ortu')->nullable(); // HP Orang Tua - Encrypted
            $table->string('nomor_wa_ortu_2')->nullable(); // HP Orang Tua 2 - Encrypted
            $table->string('foto')->nullable();
            $table->date('tanggal_lahir')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('profiles');
    }
};

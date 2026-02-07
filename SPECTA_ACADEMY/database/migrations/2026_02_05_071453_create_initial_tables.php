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
        // Membuat tabel profiles untuk menyimpan data detail siswa
        Schema::create('profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->date('tanggal_lahir');
            $table->text('alamat'); // <--- Kolom alamat yang tadi hilang sudah ditambahkan
            $table->string('nomor_wa');
            $table->string('nomor_wa_ortu');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Menghapus tabel profiles jika migrasi dibatalkan
        Schema::dropIfExists('profiles');
    }
};

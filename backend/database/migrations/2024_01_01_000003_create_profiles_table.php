<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
   public function up(): void
{
    Schema::create('profiles', function (Blueprint $table) {
        $table->id(); // Ini ID untuk tabel profiles sendiri

        // PENGHUBUNG KE TABEL USERS (Pastikan kolom user_id dibuat dulu baru foreign key-nya)
        $table->unsignedBigInteger('user_id');
        $table->foreign('user_id')->references('user_id')->on('users')->onDelete('cascade');

        $table->string('nomor_wa')->nullable();
        $table->text('alamat')->nullable();
        $table->string('nama_ibu')->nullable();
        $table->string('nomor_wa_ortu')->nullable();
        $table->timestamps();
    });
}

    public function down(): void
    {
        Schema::dropIfExists('profiles');
    }
};

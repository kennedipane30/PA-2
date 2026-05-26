<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
   public function up()
{
    Schema::create('materis', function (Blueprint $table) {
        $table->id(); 
        $table->string('judul');
        $table->text('deskripsi')->nullable();
        $table->string('file_path');
        $table->enum('tipe', ['pdf', 'video']);
        
        $table->unsignedBigInteger('user_id'); 
        // Ubah 'user_id' di references() sesuai nama kolom di tabel users kamu
        $table->foreign('user_id')->references('user_id')->on('users')->onDelete('cascade');
        
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('materis');
    }
};

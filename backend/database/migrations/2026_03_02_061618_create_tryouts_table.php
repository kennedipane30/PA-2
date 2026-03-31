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
       // File: xxxx_create_tryouts_table.php
Schema::create('tryouts', function (Blueprint $table) {
    // Gunakan tryout_id (singular) agar konsisten dengan tabel results tadi
    $table->id('tryout_id'); 
    $table->string('title');
    $table->integer('duration');
    $table->timestamps();
});
    }

    public function down(): void
    {
        Schema::dropIfExists('tryouts');
    }
};


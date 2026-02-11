<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('students', function (Blueprint $table) {
            $table->id('studentsID'); // PK Sesuai ERD
            // FK Merujuk ke usersID
            $table->foreignId('user_id')->constrained('users', 'usersID')->onDelete('cascade');
            $table->string('school');
            $table->string('grade');
            $table->date('dob');
            $table->string('wa_ortu');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('students');
    }
};

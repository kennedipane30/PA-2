<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void {
        Schema::create('students', function (Blueprint $table) {
            // Primary Key sesuai CDM
            $table->id('student_id'); 

            // Foreign Key ke tabel users (Satu baris praktis)
            $table->foreignId('user_id')->constrained('users', 'user_id')->onDelete('cascade');

            $table->string('nisn')->nullable();
            $table->string('parent_name')->nullable();
            $table->string('school')->nullable();      // Alamat/Asal Sekolah
            $table->string('parent_phone')->nullable(); // Sebelumnya wa_ortu
            $table->date('birth_date')->nullable();    // Sebelumnya dob
            $table->string('grade')->default('12 IPA');
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('students');
    }
};
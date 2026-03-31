<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void {
        Schema::create('students', function (Blueprint $table) {
            // Sesuai CDM, gunakan student_id (atau students_id)
            $table->id('student_id'); 

            // PERBAIKAN: Ganti 'usersID' menjadi 'user_id' agar cocok dengan tabel users
            $table->foreignId('user_id')->constrained('users', 'user_id')->onDelete('cascade');

            $table->string('parent_name')->nullable(); // Nama Orang Tua
            $table->string('school')->nullable();      // Alamat/Sekolah
            $table->string('wa_ortu')->nullable();
            $table->string('nisn')->nullable();       // NISN
            $table->date('dob')->nullable();          // Tanggal Lahir
            $table->string('grade')->default('12 IPA');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('students');
    }
};
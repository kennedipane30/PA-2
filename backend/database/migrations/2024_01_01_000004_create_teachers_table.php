<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
{
   Schema::create('teachers', function (Blueprint $table) {
    // PASTIKAN ID-nya bernama 'teacher_id'
    $table->id('teacher_id'); 
    
    $table->foreignId('user_id')->constrained('users', 'user_id')->onDelete('cascade');
    $table->string('specialization');
    $table->timestamps();
});
}
    public function down(): void {
        Schema::dropIfExists('teachers');
    }
};
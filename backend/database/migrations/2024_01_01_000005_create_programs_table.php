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
    Schema::create('programs', function (Blueprint $table) {
        $table->id(); // Ini akan menjadi kolom 'id'
        $table->string('title');
        $table->text('description')->nullable();
        $table->decimal('price', 12, 2);
        $table->string('image')->nullable();
        
        // Tambahkan kolom ini jika Anda menggunakannya di Seeder
        $table->unsignedBigInteger('teachers_id')->nullable();
        $table->unsignedBigInteger('category_id')->nullable();
        $table->date('start_date')->nullable();
        $table->date('end_date')->nullable();
        
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('programs');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('classes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained('categories')->onDelete('cascade');
            $table->string('nama_kelas');
            $table->text('deskripsi');
            $table->decimal('harga', 10, 2);
            $table->string('thumbnail')->nullable();
            $table->integer('kapasitas')->default(30);
            $table->integer('enrolled_count')->default(0);
            $table->enum('status', ['active', 'inactive', 'full'])->default('active');
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('classes');
    }
};

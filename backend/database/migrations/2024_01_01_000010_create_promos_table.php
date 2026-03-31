<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('promos', function (Blueprint $table) {
            $table->id();
            $table->string('kode_promo')->unique();
            $table->decimal('diskon', 5, 2); // Percentage or fixed amount
            $table->enum('tipe_diskon', ['percentage', 'fixed'])->default('percentage');
            $table->integer('kuota')->default(0);
            $table->integer('used_count')->default(0);
            $table->date('start_date');
            $table->date('expired');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('promos');
    }
};

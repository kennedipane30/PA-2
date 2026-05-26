<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
   public function up(): void
{
    Schema::table('promos', function (Blueprint $table) {
        // Jika kolomnya bernama class_id, pastikan merujuk ke program_id
        $table->foreignId('class_id') 
              ->nullable()
              ->constrained('programs', 'program_id') // <--- Merujuk ke program_id
              ->onDelete('set null');
    });
}

    public function down(): void
    {
        Schema::table('promos', function (Blueprint $table) {
            $table->dropColumn('class_id');
        });
    }
};
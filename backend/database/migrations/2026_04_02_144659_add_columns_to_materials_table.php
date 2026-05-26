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
    Schema::table('materials', function (Blueprint $table) {
        // Jika belum ada, tambahkan kolom ini:
        if (!Schema::hasColumn('materials', 'file_path')) {
            $table->string('file_path')->nullable();
        }
        if (!Schema::hasColumn('materials', 'order_priority')) {
            $table->integer('order_priority')->default(1); // Untuk urutan materi ke-1, 2, dst
        }
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('materials', function (Blueprint $table) {
            //
        });
    }
};

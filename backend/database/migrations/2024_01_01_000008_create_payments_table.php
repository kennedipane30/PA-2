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
    Schema::create('payments', function (Blueprint $table) {
        $table->id('payment_id'); 
        $table->string('transaction_code')->unique();
        
        $table->foreignId('user_id')->constrained('users', 'user_id')->onDelete('cascade');
        $table->foreignId('program_id')->constrained('programs', 'program_id')->onDelete('cascade');

        $table->string('snap_token')->nullable(); 
        
        $table->decimal('harga_asli', 12, 2);
        $table->decimal('diskon', 12, 2)->default(0);
        
        // TAMBAHKAN BARIS INI:
        $table->decimal('total_bayar', 12, 2); 

        $table->string('bukti_bayar')->nullable(); 
        $table->enum('status', ['pending', 'verified', 'rejected'])->default('pending');
        
        $table->timestamps();
    });
}
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
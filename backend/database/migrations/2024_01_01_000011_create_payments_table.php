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
            $table->id('paymentsID'); // Primary Key tabel payments sendiri

            $table->string('transaction_code')->unique();

            // 1. Relasi ke Users
            // Karena tabel 'users' kamu pakai PK 'user_id', maka referensinya harus 'user_id'
            $table->foreignId('user_id')
                  ->constrained('users', 'user_id')
                  ->onDelete('cascade');

            // 2. Relasi ke Programs
            // Karena tabel 'programs' kamu pakai PK standar 'id', maka referensinya harus 'id'
            $table->foreignId('program_id')
                  ->constrained('programs', 'id') // <--- PERBAIKAN DI SINI
                  ->onDelete('cascade');

            $table->string('nama_pengirim');

            // 3. Relasi ke Promos
            // Karena tabel 'promos' kamu pakai PK standar 'id', maka referensinya harus 'id'
            $table->foreignId('promo_id')
                  ->nullable()
                  ->constrained('promos', 'id') // <--- PERBAIKAN DI SINI
                  ->onDelete('set null');

            $table->decimal('harga_asli', 12, 2);
            $table->decimal('diskon', 12, 2)->default(0);
            $table->decimal('total_bayar', 12, 2);
            $table->string('bukti_bayar');

            $table->enum('status', ['pending', 'verified', 'rejected'])->default('pending');
            $table->text('catatan_admin')->nullable();
            $table->timestamp('verified_at')->nullable();

            // 4. Admin yang memverifikasi (Relasi balik ke users pada kolom user_id)
            $table->foreignId('verified_by')
                  ->nullable()
                  ->constrained('users', 'user_id')
                  ->onDelete('set null');

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

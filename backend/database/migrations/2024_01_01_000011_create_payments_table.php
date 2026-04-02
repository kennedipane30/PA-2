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
            // Gunakan id() standar atau payment_id
            $table->id('payment_id'); 

            // 1. Kode Transaksi Unik (Contoh: SPK-20240401-001)
            // Ini mempermudah pencarian saat Admin cek mutasi bank
            $table->string('transaction_code')->unique();

            // 2. Relasi ke Pembayar (Siswa)
            $table->foreignId('user_id')
                  ->constrained('users', 'user_id')
                  ->onDelete('cascade');

            // 3. Relasi ke Program yang dibeli
            $table->foreignId('program_id') // Saya ubah jadi program_id agar sinkron dengan tabel programs
                  ->constrained('programs', 'program_id')
                  ->onDelete('cascade');

            // 4. Detail Pengirim (Sesuai Desain Flutter Anda)
            // Nama pemilik rekening yang melakukan transfer
            $table->string('nama_pengirim'); 
            
            // 5. Relasi ke Promo (Opsional)
            $table->foreignId('promo_id')
                  ->nullable()
                  ->constrained('promos', 'promo_id')
                  ->onDelete('set null');

            // 6. Detail Harga
            $table->decimal('harga_asli', 12, 2);
            $table->decimal('diskon', 12, 2)->default(0);
            $table->decimal('total_bayar', 12, 2);

            // 7. Bukti Bayar (Path ke file gambar)
            $table->string('bukti_bayar');

            // 8. Status & Audit
            $table->enum('status', ['pending', 'verified', 'rejected'])->default('pending');
            $table->text('catatan_admin')->nullable(); // Alasan jika ditolak
            $table->timestamp('verified_at')->nullable();

            // 9. Admin yang memverifikasi
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
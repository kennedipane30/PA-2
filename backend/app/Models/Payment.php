<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
<<<<<<< HEAD
    protected $table = 'payments';
    protected $primaryKey = 'payment_id';

    // PASTIKAN harga_asli DAN diskon ADA DI SINI
    protected $fillable = [
        'transaction_code',
        'user_id',
        'program_id',
        'snap_token',
        'harga_asli', // Tambahkan ini
        'diskon',     // Tambahkan ini
        'total_bayar',
        'bukti_bayar',
        'status',
    ];



    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }

    public function program()
    {
        return $this->belongsTo(Program::class, 'program_id', 'program_id');
    }
}
=======
    // Beritahu Laravel nama Primary Key sesuai ERD
    protected $primaryKey = 'paymentsID';

    protected $fillable = [
        'transaction_code',
        'user_id',
        'class_id',
        'nama_pengirim',
        'total_bayar',
        'bukti_bayar',
        'status',
        'verified_by'
    ];
}
>>>>>>> b33bd9ca539f5e9c5320c729d852cb06393aaa54

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
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

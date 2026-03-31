<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $fillable = [
        'user_id',
        'class_id',
        'promo_id',
        'harga_asli',
        'diskon',
        'total',
        'bukti_bayar',
        'status',
        'catatan_admin',
        'verified_at',
        'verified_by',
    ];

    protected $casts = [
        'harga_asli' => 'decimal:2',
        'diskon' => 'decimal:2',
        'total' => 'decimal:2',
        'verified_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function class()
    {
        return $this->belongsTo(Classes::class, 'class_id');
    }

    public function verifiedBy()
    {
        return $this->belongsTo(User::class, 'verified_by');
    }
}

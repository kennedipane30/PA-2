<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

    class Enrollment extends Model {
        protected $fillable = [
        'user_id',
        'class_id',
        'payment_proof',
        'status',
        'expires_at'
    ];

    // Casting agar expires_at dibaca sebagai tanggal oleh Laravel
    protected $casts = [
        'expires_at' => 'datetime',
    ];
    }


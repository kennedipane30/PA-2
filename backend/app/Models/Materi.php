<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Materi extends Model
{
    use HasFactory;

    // Nama tabel di database
    protected $table = 'materis';

    // Kolom yang boleh diisi (Mass Assignment)
    protected $fillable = [
        'judul',
        'deskripsi',
        'file_path',
        'tipe',
        'user_id',
    ];

    /**
     * Relasi: Satu materi dimiliki oleh satu pengajar (User).
     */
    public function pengajar()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
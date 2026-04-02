<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Student extends Model
{
    use HasFactory;

    protected $table = 'students';
    protected $primaryKey = 'student_id';

    /**
     * DAFTAR KOLOM YANG BOLEH DIISI (Mass Assignment)
     * Pastikan kolom 'address' sudah ada di sini agar data Alamat tersimpan.
     */
    protected $fillable = [
        'user_id', 
        'nisn', 
        'parent_name',  // Ini untuk Nama Orang Tua
        'school', 
        'parent_phone', // Ini untuk WA Orang Tua
        'address',      // <--- TAMBAHKAN INI (Untuk Alamat)
        'birth_date', 
        'grade'
    ];

    /**
     * Relasi ke Tabel User (Satu profil Student dimiliki oleh satu User)
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }
}
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Promo extends Model {
    use HasFactory;

    // Tambahkan 'class_id' ke dalam fillable agar bisa disimpan ke database
    protected $fillable = [
        'class_id', // <--- PENTING: Tambahkan ini untuk relasi target kelas
        'kode_promo', 
        'diskon', 
        'tipe_diskon', 
        'kuota', 
        'used_count', 
        'start_date', 
        'expired', 
        'is_active'
    ];

    /**
     * Relasi ke Model ClassModel
     * Digunakan untuk mengetahui promo ini berlaku untuk kelas mana.
     * Jika hasilnya NULL, berarti promo berlaku GLOBAL (Semua Kelas).
     */
    public function classModel()
    {
        // Parameter kedua: 'class_id' adalah kolom di tabel promos
        // Parameter ketiga: 'class_id' adalah primary key di tabel classes
return $this->belongsTo(ClassModel::class, 'class_id', 'id');    }
}
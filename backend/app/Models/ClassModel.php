<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ClassModel extends Model
{
    // 1. Tentukan tabel yang benar (Tadi ada dua, sekarang kita pakai programs)
    protected $table = 'programs';

    // 2. Tentukan Primary Key yang benar (Sesuai migrasi no 05)
    protected $primaryKey = 'id';

    // 3. Tambahkan kolom yang bisa diisi (Fillable)
    protected $fillable = [
        'teachers_id',
        'category_id',
        'title',
        'description',
        'price',
        'start_date',
        'end_date',
        'harga_promo', // Tambahkan ini jika Anda menggunakan fitur promo
        'is_promo',
        'pesan_promo',
        'image'
    ];

    /**
     * Relasi ke Kategori
     */
    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    /**
     * Relasi ke Pengajar
     */
    public function teacher()
    {
        // Pastikan parameter ketiga adalah primary key di tabel teachers
        return $this->belongsTo(Teacher::class, 'teachers_id', 'teacher_id');
    }

    /**
     * Relasi ke Jadwal
     */
    public function schedules()
    {
        // Karena sekarang Primary Key kita bernama 'id', 
        // maka foreign key di tabel schedules (class_id) akan mengacu ke 'id' di sini
        return $this->hasMany(Schedule::class, 'class_id', 'id');
    }
}
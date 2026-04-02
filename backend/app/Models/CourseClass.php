<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CourseClass extends Model
{
    // 1. PAKSA MODEL MENGGUNAKAN TABEL 'programs'
    protected $table = 'programs';

    // 2. SESUAIKAN PRIMARY KEY (Jika di migrasi pakai $table->id(), maka di sini 'id')
    // Jika di migrasi Anda pakai $table->id('class_id'), biarkan 'class_id'
    protected $primaryKey = 'id'; 

    protected $fillable = [
        'title',
        'description',
        'price',
        'image',
        'teachers_id',
        'category_id',
        'start_date',
        'end_date',
        'harga_promo',
         'is_promo',
          'pesan_promo'
    ];
  // Relasi ke Kategori
    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }
}
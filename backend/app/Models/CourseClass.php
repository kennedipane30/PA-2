<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CourseClass extends Model
{
    use HasFactory;

    // WAJIB: Beritahu Laravel bahwa model ini menggunakan tabel 'classes'
    protected $table = 'classes';

    // WAJIB: Beritahu Laravel bahwa primary key-nya adalah 'class_id'
    protected $primaryKey = 'class_id';

    // Kolom yang boleh diisi
    protected $fillable = [
        'teachers_id',
        'category_id',
        'title',
        'description',
        'price',
        'start_date',
        'end_date',
    ];

    // Relasi ke Kategori
    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }
}
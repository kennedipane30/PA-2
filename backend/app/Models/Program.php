<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Program extends Model
{
    protected $table = 'programs';

    // BARIS INI WAJIB ADA AGAR TIDAK MENCARI KOLOM "id"
    protected $primaryKey = 'program_id'; 

    // Beritahu Laravel bahwa kuncinya bukan 'id'
    public $incrementing = true;

    protected $fillable = [
        'title',
        'description',
        'price',
        'image',
        'teachers_id',
        'category_id',
        'start_date',
        'end_date',
    ];
    /**
     * Relasi ke Guru (Teacher)
     */
    public function teacher()
    {
        return $this->belongsTo(Teacher::class, 'teachers_id', 'teacher_id');
    }

    /**
     * Relasi ke Kategori
     */
    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id', 'category_id');
    }
}
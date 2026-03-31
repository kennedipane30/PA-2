<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Classes extends Model
{
    use SoftDeletes;

    protected $table = 'classes';

    protected $fillable = [
        'category_id',
        'nama_kelas',
        'deskripsi',
        'harga',
        'thumbnail',
        'kapasitas',
        'enrolled_count',
        'status',
        'start_date',
        'end_date',
    ];

    protected $casts = [
        'harga' => 'decimal:2',
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function enrollments()
    {
        return $this->hasMany(Enrollment::class, 'class_id');
    }

    public function materials()
    {
        return $this->hasMany(Material::class, 'class_id');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Material extends Model
{
    // SESUAIKAN: Nama primary key di tabel materials
    protected $primaryKey = 'materialsID';

    protected $fillable = [
        'class_id',
        'title',
        'file_path',
        'order_priority' // Tambahkan ini agar bisa disimpan ke DB
    ];

    // Relasi ke Program Kelas
    public function classModel()
    {
        return $this->belongsTo(ClassModel::class, 'class_id', 'id');
    }
}

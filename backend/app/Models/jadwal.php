<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Jadwal extends Model
{
    protected $table = 'jadwals'; // Sesuaikan nama tabel di database Anda
    protected $primaryKey = 'id';

    protected $fillable = [
        'class_id',
        'user_id',
        'material_title',
        'date',
        'start_time',
        'end_time',
    ];

    // Relasi ke Program Kelas
    public function program()
    {
        // Ganti CourseClass sesuai model program Anda, dan 'class_id' adalah FK di tabel jadwals
        return $this->belongsTo(CourseClass::class, 'class_id', 'id');
    }

    // Relasi ke User (Guru)
    public function teacher()
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Enrollment extends Model {
    // 1. Sesuaikan Primary Key
    protected $primaryKey = 'enrollment_id';

    // 2. MODIFIKASI: Pastikan nama kolom SAMA dengan di Database (pgAdmin)
    // Ubah class_id menjadi program_id
    // Tambahkan enrolled_at
    protected $fillable = [
        'user_id', 
        'program_id', 
        'status', 
        'enrolled_at'
    ];

    /**
     * Relasi ke User
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    /**
     * Relasi ke Program (Course)
     * Pastikan menggunakan foreign key 'program_id'
     */
    public function program() {
        // Ganti CourseClass::class dengan nama Model Program Anda (misal Program::class)
        return $this->belongsTo(Program::class, 'program_id', 'id');
    }
}
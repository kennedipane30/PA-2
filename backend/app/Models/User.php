<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $primaryKey = 'user_id'; 
    public $incrementing = true;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'password',
        'role_id',
        'otp_code',
        'is_verified',
    ];

    protected $hidden = [
        'password',
        'remember_token',
        'otp_code',
    ];

    // ==========================================
    // 1. SCOPES (Untuk mempermudah pemanggilan di Controller)
    // ==========================================

    /**
     * Scope untuk mengambil hanya Siswa
     */
    public function scopeStudents($query)
    {
        return $query->whereHas('role', function($q) {
            $q->where('nama_role', 'student')->orWhere('nama_role', 'siswa');
        })->orWhere('role_id', 3); // Fallback jika ID-nya 3
    }

    /**
     * Scope untuk mengambil hanya Pengajar
     */
    public function scopeTeachers($query)
    {
        return $query->whereHas('role', function($q) {
            $q->where('nama_role', 'teacher')->orWhere('nama_role', 'pengajar');
        })->orWhere('role_id', 2); // Fallback jika ID-nya 2
    }

    // ==========================================
    // 2. RELASI
    // ==========================================

    public function student()
    {
        return $this->hasOne(Student::class, 'user_id', 'user_id');
    }

    public function teacher()
    {
        return $this->hasOne(Teacher::class, 'user_id', 'user_id');
    }

    public function enrollments() 
    {
        return $this->hasMany(Enrollment::class, 'user_id', 'user_id');
    }

    public function role()
    {
        // Pastikan tabel roles menggunakan primary key 'role_id'
        return $this->belongsTo(Role::class, 'role_id', 'role_id');
    }
}
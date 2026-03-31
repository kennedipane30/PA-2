<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    // 1. SINKRONISASI PRIMARY KEY (WAJIB)
    // Sesuai migrasi terakhir: $table->id('user_id');
    protected $primaryKey = 'user_id'; 

    // Beritahu Laravel bahwa PK-nya bertipe auto-increment
    public $incrementing = true;

    // 2. DAFTAR KOLOM YANG BOLEH DIISI (Sesuai CDM)
    protected $fillable = [
        'name',
        'email',
        'phone',
        'password',
        'role_id',
        'otp_code',
        'is_verified',
        'user_id'
    ];

    // Sembunyikan kolom sensitif saat dikirim ke Flutter (API)
    protected $hidden = [
        'password',
        'remember_token',
        'otp_code',
    ];

    // --- RELASI SESUAI STRUKTUR BARU ---

    /**
     * Relasi ke Tabel Student (Satu User memiliki satu profil Student)
     */
    public function student()
    {
        return $this->hasOne(Student::class, 'user_id', 'user_id');
    }

    /**
     * Relasi ke Tabel Teacher (Satu User memiliki satu profil Teacher)
     */
    public function teacher()
    {
        return $this->hasOne(Teacher::class, 'user_id', 'user_id');
    }

    /**
     * Relasi ke Tabel Role (Admin, Siswa, Guru)
     */
    public function role()
    {
        // Hubungkan role_id di tabel users ke rolesID di tabel roles
        return $this->belongsTo(Role::class, 'role_id', 'rolesID');
    }
}
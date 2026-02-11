<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $primaryKey = 'usersID';

    protected $fillable = [
        'name', 'email', 'phone', 'role_id', 'password',
    ];

    protected $hidden = [
        'password', 'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            // 'password' => 'hashed',  <-- HAPUS ATAU KOMENTAR BARIS INI (PENTING!)
        ];
    }

    public function student() {
        return $this->hasOne(Student::class, 'user_id', 'usersID');
    }

    public function role() {
        return $this->belongsTo(Role::class, 'role_id', 'rolesID');
    }
}

<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens; // 1. Tambahkan ini untuk API Token

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasApiTokens, HasFactory, Notifiable; // 2. Gunakan HasApiTokens di sini

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'role_id',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    // --- RELASI DATABASE (PENTING UNTUK PROJECT INI) ---

    /**
     * Relasi ke tabel Roles (Satu User punya satu Role)
     */
    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    /**
     * Relasi ke tabel Profiles (Satu User punya satu Profil detail)
     */
    public function profile()
    {
        return $this->hasOne(Profile::class);
    }

    /**
     * Relasi ke tabel OtpCodes
     */
    public function otpCodes()
    {
        return $this->hasMany(OtpCode::class);
    }
}

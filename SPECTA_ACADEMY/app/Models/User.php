<?php
namespace App\Models;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable {
    use HasApiTokens, Notifiable;
    protected $primaryKey = 'usersID';
    protected $fillable = ['role_id', 'name', 'email', 'phone', 'password', 'is_verified'];
    protected $hidden = ['password', 'remember_token'];

    public function student() { return $this->hasOne(Student::class, 'user_id', 'usersID'); }
    public function role() { return $this->belongsTo(Role::class, 'role_id', 'rolesID'); }
}

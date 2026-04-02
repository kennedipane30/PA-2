<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class User extends Authenticatable
{
    protected $table = 'users';
    protected $primaryKey = 'user_id'; // Pastikan ini

    protected $fillable = [
        'name',
        'email',
        'password', // Pastikan ini ada
        'role_id',
        'phone',
        'is_verified',
    ];
    
    // ...
}

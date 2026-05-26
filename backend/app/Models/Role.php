<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    protected $table = 'roles';
    // PASTIKAN BARIS INI ADA:
    protected $primaryKey = 'role_id'; 

      protected $fillable = ['nama_role'];
}
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Profile extends Model
{
    protected $fillable = [
        'user_id',
        'tanggal_lahir',
        'nomor_wa',
        'nomor_wa_ortu'
    ];
}

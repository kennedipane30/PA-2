<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OtpCode extends Model
{
    protected $table = 'otp_codes';

    // PASTIKAN SEMUA INI ADA DI DALAM ARRAY:
    protected $fillable = [
        'user_id', 
        'email', 
        'otp_code', 
        'expired_at', 
        'is_used'
    ];
}
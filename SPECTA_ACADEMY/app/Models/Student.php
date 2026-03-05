<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    protected $primaryKey = 'studentsID'; // Pastikan PK benar

    protected $fillable = [
        'user_id',
        'parent_name', // TAMBAHKAN INI
        'school',
        'wa_ortu',
        'nisn',
        'dob',
        'grade',
    ];
}
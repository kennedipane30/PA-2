<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    protected $table = 'students';
    protected $primaryKey = 'student_id';

    protected $fillable = [
        'user_id',
        'parent_name',
        'school',
        'wa_ortu',
        'nisn',
        'dob',
        'grade',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }
}
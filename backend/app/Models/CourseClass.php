<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CourseClass extends Model
{
    use HasFactory;

    protected $table = 'programs';

    // 1. SESUAIKAN DENGAN HASIL TINKER
    protected $primaryKey = 'program_id'; 

    protected $fillable = [
        'title', 
        'description', 
        'price', 
        'image',
        'teachers_id', // Berdasarkan list tinker Anda
        'category_id'  
    ];

    protected $casts = [
        'price' => 'integer',
    ];
}
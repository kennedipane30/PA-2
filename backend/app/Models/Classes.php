<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CourseClass extends Model
{
    protected $table = 'classes';
    protected $primaryKey = 'class_id';
    protected $fillable = [
        'teachers_id',
        'category_id',
        'title',
        'description',
        'price',
        'start_date',
        'end_date'
    ];

    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    public function teacher()
    {
        return $this->belongsTo(Teacher::class, 'teachers_id', 'teacher_id');
    }
}
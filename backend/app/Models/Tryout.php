<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tryout extends Model
{
    protected $primaryKey = 'tryout_id';

    protected $fillable = [
        'class_id',
        'title',
        'duration'
    ];

    public function questions()
    {
        return $this->hasMany(Question::class, 'tryout_id', 'tryout_id');
    }

    public function classModel()
    {
        return $this->belongsTo(ClassModel::class, 'class_id', 'class_id');
    }
}

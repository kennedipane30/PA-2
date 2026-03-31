<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Teacher extends Model {
    protected $table = 'teachers';
    protected $primaryKey = 'teacher_id';
    protected $fillable = ['user_id', 'specialization'];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }
}
<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Enrollment extends Model {
    protected $fillable = ['user_id', 'class_id', 'status'];

    public function user() { return $this->belongsTo(User::class); }
    public function classModel() { return $this->belongsTo(ClassModel::class, 'class_id'); }
}

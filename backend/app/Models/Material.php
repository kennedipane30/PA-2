<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Material extends Model {
    protected $primaryKey = 'material_id';
    protected $fillable = ['class_id', 'teacher_id', 'title', 'type', 'file_path'];

    public function classModel()
    {
        return $this->belongsTo(ClassModel::class, 'class_id', 'class_id');
    }

    public function teacher()
    {
        return $this->belongsTo(Teacher::class, 'teacher_id', 'teacher_id');
    }
}

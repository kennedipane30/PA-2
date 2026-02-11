<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Student extends Model {
    protected $primaryKey = 'studentsID'; // Beritahu Laravel PK-nya
    protected $fillable = ['user_id', 'school', 'grade', 'dob', 'wa_ortu'];
}

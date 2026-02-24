<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Tryout extends Model {
    protected $primaryKey = 'tryoutsID'; // Sesuai ERD
    protected $fillable = ['class_id', 'title'];
}

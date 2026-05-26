<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    // Tambahkan baris ini
    protected $primaryKey = 'category_id';

    protected $fillable = ['name', 'slug'];
}
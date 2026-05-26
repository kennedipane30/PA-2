<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Gallery extends Model
{
    use HasFactory;

    // Jika di migrasi menggunakan $table->id(), maka PK defaultnya adalah 'id'
    // Tapi jika Anda ingin mengubahnya menjadi 'galleriesID' di migrasi, tambahkan ini:
    // protected $primaryKey = 'galleriesID';

    protected $fillable = ['judul', 'foto', 'deskripsi'];
}
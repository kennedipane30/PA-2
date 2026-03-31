<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Question extends Model {
    protected $primaryKey = 'question_id';
    protected $fillable = ['tryout_id', 'question', 'option_a', 'option_b', 'option_c', 'option_d', 'correct_answer', 'explanation'];

    public function tryout()
    {
        return $this->belongsTo(Tryout::class, 'tryout_id', 'tryout_id');
    }
}

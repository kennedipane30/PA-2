<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class TryoutResult extends Model {
    protected $primaryKey = 'result_id';
    protected $fillable = ['user_id', 'tryout_id', 'score', 'correct_answers', 'wrong_answers', 'unanswered'];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }

    public function tryout()
    {
        return $this->belongsTo(Tryout::class, 'tryout_id', 'tryout_id');
    }
}

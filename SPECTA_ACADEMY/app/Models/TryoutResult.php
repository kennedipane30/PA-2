<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class TryoutResult extends Model {
    protected $primaryKey = 'resultsID';
    protected $fillable = ['user_id', 'tryout_id', 'score', 'total_correct'];
    public function tryout() { return $this->belongsTo(Tryout::class, 'tryout_id', 'tryoutsID'); }
}

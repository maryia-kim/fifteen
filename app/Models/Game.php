<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Game extends Model
{
    public $fillable = ['user_id', 'board', 'solved_at'];
    public $casts = [
        'board' => 'array'
    ];

    public function user()
    {
        return $this->belongsTo('App\Models\User');
    }
}

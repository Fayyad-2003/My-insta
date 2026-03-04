<?php

namespace App\Models\Reel;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class ReelLike extends Model
{
    //
    protected $fillable = [
        'liker_id',
        'reel_id',
    ];

    public function reel()
    {
        return $this->belongsTo(Reel::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

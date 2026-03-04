<?php

namespace App\Models\Reel;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class ReelView extends Model
{
    //
    protected $fillable = [
        'reel_id',
        'user_id',
        'viewed_at'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function reel()
    {
        return $this->belongsTo(Reel::class);
    }
}

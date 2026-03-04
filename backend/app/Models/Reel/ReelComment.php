<?php

namespace App\Models\Reel;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class ReelComment extends Model
{
    //
    protected $fillable = [
        'reel_id',
        'user_id',
        'text',
        'parent_comment_id'
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

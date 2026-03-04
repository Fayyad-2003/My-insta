<?php

namespace App\Models\Reel;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class ReelCommentLike extends Model
{
    //
    protected $fillable = [
        'comment_id',
        'liker_id'
    ];

    public function comment()
    {
        return $this->belongsTo(ReelComment::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

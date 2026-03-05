<?php

namespace App\Models\Story;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class StoryLike extends Model
{
    //
    protected $fillable = [
        'liker_id',
        'story_id'
    ];

    public function story()
    {
        return $this->belongsTo(Story::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'liker_id');
    }
}

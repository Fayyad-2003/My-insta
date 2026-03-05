<?php

namespace App\Models\Story;

use Illuminate\Database\Eloquent\Model;

class StoryMedia extends Model
{
    //
    protected $fillable = [
        'story_id',
        'type',
        'url',
        'thumbnail_url'
    ];

    public function story()
    {
        return $this->belongsTo(Story::class);
    }
}

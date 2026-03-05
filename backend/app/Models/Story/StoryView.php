<?php

namespace App\Models\Story;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class StoryView extends Model
{
    //
    protected $fillable = [
        'viewer_id',
        'story_id',
        'viewed_at'
    ];

    protected $casts = [
        'viewed_at' => 'datetime'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'viewer_id');
    }
}

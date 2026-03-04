<?php

namespace App\Models\Post;

use Illuminate\Database\Eloquent\Model;

class PostSetting extends Model
{
    //
    protected $fillable = [
        'post_id',
        'visibility',
        'allow_comments',
        'allow_share',
        'allow_reactions',
        'post_type',
        'scheduled_at',
        'expires_at',
        'is_pinned',
        'allow_tagging',
        'allow_mentions',
        'has_location',
        'notify_on_comment',
        'notify_on_like',
        'notify_on_share',
        'caption_color',
        'caption_size'
    ];

    public function post()
    {
        return $this->belongsTo(Post::class);
    }
}

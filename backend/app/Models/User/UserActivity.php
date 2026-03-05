<?php

namespace App\Models\User;

use App\Models\Post\Post;
use App\Models\Post\PostComment;
use App\Models\Post\PostCommentLike;
use App\Models\Reel\Reel;
use App\Models\Reel\ReelComment;
use App\Models\Reel\ReelCommentLike;
use App\Models\Story\Story;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class UserActivity extends Model
{
    protected $fillable = [
        'user_id',
        'target_type',
        'target_id',
        'parent_type',
        'parent_id'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function targetConent()
    {
        return match ($this->target_type) {
            'post' => tap($this->belongsTo(Post::class, 'target_id')),
            'post_comment' => tap($this->belongsTo(PostComment::class, 'target_id')),
            'post_comment_like' => tap($this->belongsTo(PostCommentLike::class, 'target_id')),
            'reel' => tap($this->belongsTo(Reel::class, 'target_id')),
            'reel_comment' => tap($this->belongsTo(ReelComment::class, 'target_id')),
            'reel_comment_like' => tap($this->belongsTo(ReelCommentLike::class, 'target_id')),
            'story' => tap($this->belongsTo(Story::class, 'target_id')),
            'user' => tap($this->belongsTo(User::class, 'target_id')),
            default => null
        };
    }
}

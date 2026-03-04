<?php

namespace App\Models\Post;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class PostComment extends Model
{
    //
    protected $fillable = [
        'post_id',
        'user_id',
        'parent_id',
        'content',
        'is_approved'
    ];

    public function post()
    {
        return $this->belongsTo(Post::class);
    }

    // Parent Comment

    public function parent()
    {
        return $this->belongsTo(PostComment::class, 'parent_id');
    }

    public function replies()
    {
        return $this->belongsTo(PostComment::class, 'parent_id');
    }

    public function likes()
    {
        return $this->belongsTo(PostCommentLike::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

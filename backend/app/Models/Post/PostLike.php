<?php

namespace App\Models\Post;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class PostLike extends Model
{
    //

    protected $fillable = [
        'user_id',
        'post_id'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function post()
    {
        return $this->belongsTo(Post::class);
    }

    public function preview()
    {
        // $parentPostPreview = $this->relationLoaded('post');
        // return $parentPostPreview;
    }
}

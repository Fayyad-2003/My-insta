<?php

namespace App\Models\User;

use App\Models\Post\Post;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class UserPost extends Model
{
    //
    protected $fillable = [
        'user_id',
        'postable_id',
        'postable_type',
        'type'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function post()
    {
        return $this->morphTo(Post::class);
    }
}

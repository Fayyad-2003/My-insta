<?php

namespace App\Models\Post;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class PostShare extends Model
{
    //
    protected $fillable = [
        'shared_to',
        'post_id',
        'shared_at',
        'shared_by_id',
        'platform'
    ];

    // Relationships

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function post()
    {
        return $this->belongsTo(Post::class);
    }
}

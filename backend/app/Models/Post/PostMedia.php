<?php

namespace App\Models\Post;

use Illuminate\Database\Eloquent\Model;

class PostMedia extends Model
{
    //
    protected $fillable = [
        'post_id',
        'type',
        'url',
        'captoin'
    ];

    public function post()
    {
        return $this->belongsTo(Post::class);
    }

}

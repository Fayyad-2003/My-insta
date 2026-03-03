<?php

namespace App\Models\Post;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    //
    protected $fillable = [
        'user_id',
        'content',
        'is_published',
        'tags',
        'ai_labels',
        'slug',
        'status'
    ];

    // Cast JSON fields to php arrays

    protected $casts = [
        'tags' => 'array',
        'ai_labels' => 'array'
    ];

    // Relationships

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function media()
    {
        return $this->hasMany(PostMedia::class);
    }

    public function likes()
    {
        return $this->hasMany(PostLike::class);
    }

    public function views()
    {
        return $this->hasMany(PostView::class);
    }

    public function comments()
    {
        return $this->hasMany(PostComment::class);
    }

    public function settings()
    {
        return $this->hasOne(PostSetting::class);
    }

    public function shares()
    {
        return $this->hasMany(PostShare::class);
    }

    // Post Methods

    public function preview()
    {
        $previewPost = $this->only(['id', 'slug', 'content']);
        $firstMedia = $this->media()->first();

        if ($firstMedia) {
            $previewPost['media'] = $firstMedia;
        } else {
            $previewPost['media'] = null;
        }

        $previewPost['slug'] = '/posts/' . $this->slug;
        return $previewPost;
    }
}

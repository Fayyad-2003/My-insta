<?php

namespace App\Models\Post;

use App\Models\User;
use App\Models\User\UserPost;
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
        'ai_labels' => 'array',
        'is_published' => 'boolean'
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

    public static function boot()
    {
        parent::boot();

        static::creating(function ($post) {
            if (empty($post->slug)) {
                $post->slug = \Str::random(10);
            }
        });
    }

    // Booted Method fo hadle model events

    protected static function booted()
    {
        static::creating(function ($post) {
            // Create UserPost record [reels, post]

            UserPost::create([
                'user_id' => $post->user_id,
                'postable_id' => $post->id,
                'postable_type' => self::class,
                'type' => 'post'
            ]);
        });

        static::deleted(function ($post) {
            $post->media()->delete();
            $post->likes()->delete();
            $post->views()->delete();
            $post->comments()->delete();
            $post->shares()->delete();
            $post->settings()->delete();
            $post->views()->delete();

            UserPost::where('postable_id', $post->id)
                ->where('postable_type', self::class)
                ->delete();
        });
    }
}

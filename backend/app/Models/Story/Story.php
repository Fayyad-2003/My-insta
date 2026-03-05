<?php

namespace App\Models\Story;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Story extends Model
{
    //
    use HasFactory;

    protected $fillable = [
        'content',
        'user_id',
        'slug',
        'expired_at',
        'type',
        'template'
    ];

    protected $casts = [
        'expired_at' => 'datetime'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function media()
    {
        return $this->hasOne(StoryMedia::class);
    }

    public function viewers()
    {
        return $this->hasMany(StoryView::class);
    }


    public function setting()
    {
        return $this->hasOne(StorySetting::class);
    }

    public function reactions()
    {
        return $this->hasMany(StoryReaction::class);
    }

    public function likes()
    {
        return $this->hasMany(StoryLike::class)->where('type', 'like');
    }

    protected static function booted()
    {
        static::creating(function ($story) {
            if (empty($story->slug)) {
                $story->slug = 'story-' . Str::random(10);
            }
        });

        static::deleting(function ($story) {
            $story->media()->delete();
            $story->setting()->delete();
            $story->reactions()->delete();
            $story->likes()->delete();
            $story->viewers()->delete();
        });
    }
}

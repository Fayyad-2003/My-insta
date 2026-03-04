<?php

namespace App\Models\Reel;

use App\Models\User;
use App\Models\User\UserPost;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class Reel extends Model
{
    //
    protected $fillable = [
        'user_id',
        'slug',
        'video_url',
        'thumbnail_url',
        'caption',
        'is_published',
        'music',
        'tags',
        'ai_labels'
    ];

    protected $casts = [
        'is_published' => 'boolean',
        'music' => 'array',
        'tags' => 'array',
        'ai_labels' => 'array'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function likes()
    {
        return $this->hasMany(ReelLike::class);
    }

    public function views()
    {
        return $this->hasMany(ReelView::class);
    }

    public function comments()
    {
        return $this->hasMany(ReelComment::class);
    }

    public function settings()
    {
        return $this->hasOne(ReelSetting::class);
    }

    public function shares()
    {
        return $this->hasMany(ReelShare::class);
    }

    public function preview()
    {
        $preview = $this->only(['id', 'slug', 'thumbnail_url', 'caption']);
        $preview['slug'] = '/reels/' . $this->slug;
        $preview['thumbnail_url'] = Storage::url($this->thumbnail_url);

        return $preview;
    }

    public function authCanLike(?int $userId = null): bool
    {
        $userId = $userId ?? Auth::id();
        if (!$userId) {
            return false;
        }

        return !$this->likes()->where('user_id', $userId)->exists();
    }

    public function getLikesCountAttribute(): int
    {
        if ($this->relationLoaded('likes')) {
            return $this->likes->count();
        }

        return (int) $this->likes()->count();
    }

    public function getViewsCountAttribute(): int
    {
        if ($this->relationLoaded('views')) {
            return $this->views->count();
        }

        return (int) $this->views()->count();
    }

    protected static function booted()
    {

        static::creating(function ($reel) {
            if (empty($reel->slug)) {
                $base = Str::slug(substr($reel->caption ?? 'reel', 0, 50));
                $reel->slug = $base . '-' . uniqid();
            }
        });

        static::created(function ($reel) {
            UserPost::create([
                'user_id' => $reel->user_id,
                'postable_id' => $reel->id,
                'postable_type' => self::class,
                'type' => 'reel'
            ]);
        });

        static::deleted(function ($reel) {
            UserPost::where('postable_id', $reel->id)
                ->where('postable_type', self::class)
                ->delete();

            $reel->likes()->delete();
            $reel->views()->delete();
            $reel->comments()->delete();
            $reel->shares()->delete();
            $reel->settings()->delete();
        });
    }
}


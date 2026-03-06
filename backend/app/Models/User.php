<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Models\Post\Post;
use App\Models\Reel\Reel;
use App\Models\Story\Story;
use App\Models\User\BlockedUser;
use App\Models\User\Bookmark;
use App\Models\User\Contact;
use App\Models\User\UserActivity;
use App\Models\User\UserFollows;
use App\Models\User\UserPost;
use App\Models\User\UserSetting;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use App\Models\Auth\RefreshToken;


class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'username',
        'last_seen',
        'bio',
        'slug',
        'avatar',
        'location',
        'is_verified'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'settings' => 'array',
            'last_seen' => 'datetime'
        ];
    }

    public function refreshTokens()
    {
        return $this->hasMany(RefreshToken::class);
    }

    public function contact()
    {
        return $this->hasOne(Contact::class);
    }

    public function feed()
    {
        return $this->hasMany(\App\Models\User\UserFeed::class);
    }

    public function posts()
    {
        return $this->hasMany(Post::class);
    }

    public function reels()
    {
        return $this->hasMany(Reel::class);
    }

    public function stories()
    {
        return $this->hasMany(Story::class);
    }

    public function followers()
    {
        return $this->hasMany(UserFollows::class, 'follower_id');
    }

    public function following()
    {
        return $this->hasMany(UserFollows::class, 'following_id');
    }

    public function bookmarks()
    {
        return $this->hasMany(Bookmark::class);
    }

    public function activities()
    {
        return $this->hasMany(UserActivity::class);
    }

    public function notifications()
    {
        // return $this->hasMany(Notification::class);
    }

    public function settings()
    {
        return $this->hasMany(UserSetting::class);
    }

    public function blockedUsers()
    {
        return $this->hasMany(BlockedUser::class, 'user_id');
    }

    public function blockedBy()
    {
        return $this->hasMany(BlockedUser::class, 'blocked_user_id');
    }

    public function userPosts()
    {
        return $this->hasMany(UserPost::class);
    }

    public function postsCount()
    {
        return $this->userPosts()->count();
    }

    public function hasBlocked(User $user): bool
    {
        return $this->blockedUsers()->where('blocked_user_id', $user->id)->exists();
    }

    public function isBlockedBy(User $user): bool
    {
        return $this->blockedBy()->where('user_id', $user->id)->exists();
    }

    protected static function boot()
    {
        parent::boot();

        // Generate a slug
        static::creating(function ($user) {
            if (empty($user->slug)) {
                $user->slug = \Str::slug($user->name . '-' . \Str::random(6));
            }
        });

        static::created(function ($user) {
            UserSetting::create([
                'user_id' => $user->id,
            ]);
        });

        static::deleting(function ($user) {
            $user->posts()->delete();
            $user->reels()->delete();
            $user->stories()->delete();
            $user->followers()->delete();
            $user->following()->delete();
            $user->bookmarks()->delete();
            $user->activities()->delete();
            $user->notifications()->delete();
            $user->settings()->delete();
            $user->blockedUsers()->delete();
            $user->blockedBy()->delete();
        });
    }

}


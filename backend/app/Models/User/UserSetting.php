<?php

namespace App\Models\User;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class UserSetting extends Model
{
    protected $fillable = [
        'user_id',
        'profile_visibility',
        'show_activiy_status',
        'allow_tagging',
        'allow_mentions',
        'allow_comments',
        'message_privacy',
        'recieve_notifications',
        'share_profile',
        'allow_donwloads',
        'allow_sharing_posts',
        'personalized_ads',
        'personalized_recommendations'
    ];

    protected $casts = [
        'show_activity_status' => 'boolean',
        'allow_tagging' => 'boolean',
        'allow_mentions' => 'boolean',
        'allow_comments' => 'boolean',
        'receive_notifications' => 'boolean',
        'share_profile' => 'boolean',
        'allow_downloads' => 'boolean',
        'allow_share_posts' => 'boolean',
        'personalized_ads' => 'boolean',
        'personalized_recommendations' => 'boolean'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

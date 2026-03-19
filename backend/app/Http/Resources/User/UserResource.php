<?php

namespace App\Http\Resources\User;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $authUser = $request->user();
        $hasSettings = $this->relationLoaded('setting') && $this->setting;

        $contact = $this->relationLoaded('contact') && $this->contact;
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'username' => $this->username,
            'bio' => $this->bio,
            'location' => $this->location,
            'is_verified' => $this->is_verified,
            'is_private' => false,
            'following_count' => $this->following()->count(),
            'followers_counts' => $this->followers()->count(),
            'posts_count' => $this->postsCount(),
            'avatar' => $this->avatar,
            'last_seen' => optional($this->last_seen)->toIso8601String(),
            'contact' => $contact,
            'settings' => $hasSettings ? [
                'privacy' => $this->setting->privacy,
                'allow_comments' => $this->setting->allow_comments,
                'allow_tagging' => $this->setting->allow_tagging,
            ] : null,
            'is_following' => $authUser ? \App\Models\User\UserFollows::where('follower_id', $authUser->id)->where('following_id', $this->id)->exists() : false,
            'is_blocked_by' => $authUser ? $this->isBlockedBy($authUser) : false,
            'created_at' => $this->created_at,
        ];
    }
}

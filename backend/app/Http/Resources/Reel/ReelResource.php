<?php

namespace App\Http\Resources\Reel;

use App\Http\Resources\User\UserPreviewResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ReelResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'caption' => $this->caption,
            'video_url' => $this->video_url,
            'slug' => $this->slug,
            'is_published' => $this->is_published,
            'total_likes' => 0,
            'total_shares' => 0,
            'music' => $this->music,
            'thumbnail_url' => $this->thumbnail_url,
            'settings' => $this->setting,
            'user' => new UserPreviewResource($this->user)
        ];
    }
}

<?php

namespace App\Http\Resources\Post;

use App\Http\Resources\User\UserPreviewResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PostResource extends JsonResource
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
            'content' => $this->content,
            'media' => $this->media,
            'slug' => $this->slug,
            'is_published' => $this->is_published,
            'total_likes' => 0,
            'total_shares' => 0,
            'settings' => $this->setting,
            'user' => new UserPreviewResource($this->user)
        ];
    }
}

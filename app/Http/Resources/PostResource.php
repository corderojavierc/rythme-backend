<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Models\User;
use App\Models\Music;

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
            'profile_image' => $this->user?->profile_image,
            'name' => $this->user?->name,
            'second_name' => $this->user?->second_name,
            'user_name' => $this->user?->username,
            'cover_url' => $this->music?->cover_url,
            'music' => $this->music?->title,
            'artist' => $this->music?->artist,
            'rating' => $this->rating,
            'title' => $this->text,
            'count_liked' => $this->count_likes,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}

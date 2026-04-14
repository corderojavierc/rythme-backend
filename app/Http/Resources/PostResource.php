<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

final class PostResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $post = $this->resource;

        return [
            'id' => $post->id,
            'profile_image' => $post->user?->profile_image,
            'name' => $post->user?->name,
            'second_name' => $post->user?->second_name,
            'user_name' => $post->user?->username,
            'cover_url' => $post->music?->cover_url,
            'music' => $post->music?->title,
            'artist' => $post->music?->artist,
            'rating' => $post->rating,
            'title' => $post->text,
            'count_likes' => $post->count_likes,
            'is_liked' => is_null($post->is_liked) ? null : (bool) $post->is_liked,
            'created_at' => $post->created_at,
            'updated_at' => $post->updated_at,
        ];
    }
}

<?php

declare(strict_types=1);

namespace App\Http\Resources;

use App\Models\MusicRating;
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
        $rating = MusicRating::query()->find($post->music_id);

        return [
            'id' => $post->id,
            'profile_image' => $post->user?->profile_image,
            'name' => $post->user?->name,
            'second_name' => $post->user?->second_name,
            'user_name' => $post->user?->username,
            'followers' => $post->user?->followers,
            'following' => $post->user?->following,
            'posts' => $post->user?->posts,
            'user_id' => $post->user?->id,
            'user_type' => $post->user?->type,
            'music_id' => $post->music?->id,
            'cover_url' => $post->music?->cover_url,
            'music' => $post->music?->title,
            'artist' => $post->music?->artist,
            'rating' => $post->rating,
            'title' => $post->text,
            'count_likes' => $post->count_likes,
            'count_comments' => $post->count_comments,
            'is_liked' => is_null($post->is_liked) ? null : (bool) $post->is_liked,
            'global_rating' => $rating->rating ?? '',
            'count_ratings' => $rating->count_ratings ?? 0,
            'is_valorated' => (bool) $post->is_valorated,
            'created_at' => $post->created_at,
            'updated_at' => $post->updated_at,
        ];
    }
}

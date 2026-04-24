<?php

declare(strict_types=1);

namespace App\Http\Resources;

use App\Models\Music;
use App\Models\MusicRating;
use App\Models\Post; // Importamos el modelo Post
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

final class MusicResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        /** @var Music $music */
        $music = $this->resource;
        $ratingInfo = MusicRating::where('music_id', $music->id)->first();
        $userHasPost = false;

        if (auth()->check()) {
            $userHasPost = Post::where('music_id', $music->id)
                ->where('user_id', auth()->id())
                ->exists();
        }

        return [
            'id' => $music->id,
            'title' => $music->title,
            'artist' => $music->artist,
            'cover_url' => $music->cover_url,
            'rating' => $ratingInfo->rating ?? 0,
            'count_ratings' => $ratingInfo->count_ratings ?? 0,
            'is_valorated' => $userHasPost,
            'release_date' => $music->release_date,
            'created_at' => $music->created_at,
        ];
    }
}

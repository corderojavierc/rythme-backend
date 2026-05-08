<?php

declare(strict_types=1);

namespace App\Http\Resources;

use App\Models\Music;
use App\Models\MusicRating;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property-read int|null $rank_position
 * @property-read int|null $position
 * @property-read float $rating
 * @property-read int $count_ratings
 * @property-read string $music_id
 */
final class RankingResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $music = Music::query()->findOrFail($this->music_id);
        $ratingInfo = MusicRating::query()->where('music_id', $music->id)->first();
        $userHasPost = false;

        if (auth()->check()) {
            $userHasPost = Post::query()->where('music_id', $music->id)
                ->where('user_id', auth()->id())
                ->exists();
        }

        return [
            'position' => $this->rank_position ?? $this->position ?? null,
            'rating' => round((float) $this->rating, 2),
            'count_ratings' => (int) $this->count_ratings,
            'music' => [
                'id' => $music->id,
                'title' => $music->title,
                'artist' => $music->artist,
                'spotify_artist_ids' => $music->spotify_artist_ids,
                'cover_url' => $music->cover_url,
                'rating' => $ratingInfo->rating ?? 0,
                'count_ratings' => $ratingInfo->count_ratings ?? 0,
                'is_valorated' => $userHasPost,
            ],
        ];
    }
}

<?php

declare(strict_types=1);

namespace App\Http\Resources;

use App\Models\Music;
use App\Models\MusicRating;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

final class MusicResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        /** @var Music $music */
        $music = $this->resource;
        $rating = MusicRating::find($music->id);

        return [
            'id' => $music->id,
            'title' => $music->title,
            'artist' => $music->artist,
            'cover_url' => $music->cover_url,
            'rating' => $rating->rating ?? '',
            'count_ratings' => $rating->count_ratings ?? 0,
            'release_date' => $music->release_date,
            'created_at' => $music->created_at,
        ];
    }
}

<?php

declare(strict_types=1);

namespace App\Http\Resources;

use App\Models\Music;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property-read int|null $rank_position
 * @property-read int|null $position
 * @property-read float $rating
 * @property-read int $count_ratings
 * @property-read Music $music
 */
final class RankingResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'position' => $this->rank_position ?? $this->position,
            'rating' => round((float) $this->rating, 2),
            'count_ratings' => (int) $this->count_ratings,
            'music' => [
                'id' => $this->music->id,
                'title' => $this->music->title,
                'artist' => $this->music->artist,
                'cover_url' => $this->music->cover_url,
            ],
        ];
    }
}

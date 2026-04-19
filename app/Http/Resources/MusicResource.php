<?php

declare(strict_types=1);

namespace App\Http\Resources;

use App\Models\Music;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

final class MusicResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        /** @var Music $music */
        $music = $this->resource;

        return [
            'id' => $music->id,
            'title' => $music->title,
            'artist' => $music->artist,
            'cover_url' => $music->cover_url,
            'release_date' => $music->release_date,
            'created_at' => $music->created_at,
        ];
    }
}

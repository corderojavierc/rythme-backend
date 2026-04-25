<?php

declare(strict_types=1);

namespace App\Http\Resources;

use App\Models\Comment;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

final class ItemsLikedResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $item = $this->resource;

        if ($item instanceof Post) {
            return array_merge(
                ['type' => 'post'],
                new PostResource($item)->toArray($request)
            );
        }

        if ($item instanceof Comment) {
            return array_merge(
                ['type' => 'comment'],
                new CommentResource($item)->toArray($request)
            );
        }

        return [];
    }
}

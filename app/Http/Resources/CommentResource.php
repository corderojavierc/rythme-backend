<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

final class CommentResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $comment = $this->resource;

        return [
            'id' => $comment->id,
            'profile_image' => $comment->user?->profile_image,
            'name' => $comment->user?->name,
            'second_name' => $comment->user?->second_name,
            'user_name' => $comment->user?->username,
            'user_id' => $comment->user_id,
            'post_id' => $comment->post_id,
            'text' => $comment->text,
            'is_liked' => is_null($comment->is_liked) ? null : (bool) $comment->is_liked,
            'count_likes' => (int) $comment->count_likes,
        ];
    }
}

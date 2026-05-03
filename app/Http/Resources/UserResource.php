<?php

declare(strict_types=1);

namespace App\Http\Resources;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

final class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        /** @var User $user */
        $user = $this->resource;

        return [
            'id' => $user->id,
            'name' => $user->name,
            'username' => $user->username,
            'type' => $user->type,
            'followers' => $user->followers,
            'following' => $user->following,
            'posts' => $user->posts,
            'musics' => $user->musics,
            'profile_image' => $user->profile_image,
            'is_following' => is_null($user->is_following_auth) ? false : (bool) $user->is_following_auth,
        ];
    }
}

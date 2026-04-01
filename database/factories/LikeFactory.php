<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Comment;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

final class LikeFactory extends Factory
{
    public function definition(): array
    {
        return [
            'id' => Str::uuid(),
            'user_id' => User::factory(),
            'likeable_type' => Comment::class,
            'likeable_id' => Comment::factory(),
        ];
    }
}

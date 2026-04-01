<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Post;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

final class CommentFactory extends Factory
{
    public function definition(): array
    {
        return [
            'id' => Str::uuid(),
            'post_id' => Post::factory(),
            'user_id' => User::factory(),
            'text' => $this->faker->paragraph(),
            'count_likes' => $this->faker->numberBetween(0, 500),
        ];
    }
}

<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Music;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

final class PostFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'music_id' => Music::factory(),
            'text' => $this->faker->paragraph(),
            'rating' => $this->faker->randomFloat(2, 0, 5),
            'count_likes' => 0,
            'count_comments' => 0,
        ];
    }
}

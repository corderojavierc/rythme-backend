<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Music;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

final class PostFactory extends Factory
{
    public function definition(): array
    {
        return [
            'id' => Str::uuid(),
            'user_id' => User::factory(),
            'music_id' => Music::factory(),
            'text' => $this->faker->paragraph(),
            // Rating entre 0.00 y 5.00
            'rating' => $this->faker->randomFloat(2, 0, 5),
            'count_likes' => $this->faker->numberBetween(0, 1000),
        ];
    }
}

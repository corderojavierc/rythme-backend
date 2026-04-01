<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

final class ArtistApplicationFactory extends Factory
{
    public function definition(): array
    {
        return [
            'id' => Str::uuid(),
            'user_id' => User::factory(),
            'artist' => $this->faker->boolean(20), // 20% de probabilidad de ser true
            'followers' => $this->faker->numberBetween(100, 100000),
            'listeners' => $this->faker->optional()->numberBetween(50, 50000),
            'youtube' => $this->faker->optional()->url(),
            'tiktok' => $this->faker->optional()->url(),
            'instagram' => $this->faker->optional()->url(),
            'spotify' => $this->faker->optional()->url(),
            'twitch' => $this->faker->optional()->url(),
            'description' => $this->faker->paragraph(),
        ];
    }
}

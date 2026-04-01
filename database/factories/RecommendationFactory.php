<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Music;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

final class RecommendationFactory extends Factory
{
    public function definition(): array
    {
        return [
            'id' => Str::uuid(),
            'user_id' => User::factory(),
            'music_id' => Music::factory(),
            'message' => $this->faker->sentence(),
        ];
    }
}

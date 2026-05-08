<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Music;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Date;

final class TopRatedMusicFactory extends Factory
{
    public function definition(): array
    {
        return [
            'period' => Date::now()->startOfMonth()->toDateString(),
            'rank_position' => $this->faker->numberBetween(1, 10),
            'music_id' => Music::factory(),
            'rating' => $this->faker->randomFloat(2, 3.00, 5.00),
            'count_ratings' => $this->faker->numberBetween(10, 10000),
        ];
    }
}

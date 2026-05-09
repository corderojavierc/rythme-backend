<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Music;
use App\Models\MusicRating;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<MusicRating>
 */
final class MusicRatingFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'music_id' => Music::factory(),
            'rating' => fake()->randomFloat(2, 0, 5),
            'count_ratings' => fake()->numberBetween(1, 1000),
        ];
    }
}

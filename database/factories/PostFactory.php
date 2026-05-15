<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Music;
use App\Models\User;
use Database\Factories\Data\TextsFactory;
use Illuminate\Database\Eloquent\Factories\Factory;

final class PostFactory extends Factory
{
    public function definition(): array
    {
        $rating = $this->faker->randomElement([0.5, 1.0, 1.5, 2.0, 2.5, 3.0, 3.5, 4.0, 4.5, 5.0]);

        $text = $rating >= 2.5
            ? $this->faker->randomElement(TextsFactory::POST_POSITIVE)
            : $this->faker->randomElement(TextsFactory::POST_NEGATIVE);

        return [
            'user_id' => User::factory(),
            'music_id' => Music::factory(),
            'text' => $text,
            'rating' => $rating,
            'count_likes' => 0,
            'count_comments' => 0,
        ];
    }

    public function lastMonths(int $months = 2): static
    {
        return $this->state(fn (): array => [
            'created_at' => $this->faker->dateTimeBetween(sprintf('-%d months', $months), '-1 month'),
            'updated_at' => $this->faker->dateTimeBetween(sprintf('-%d months', $months), '-1 month'),
        ]);
    }
}

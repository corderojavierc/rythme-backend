<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\ArtistApplication;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ArtistApplication>
 */
final class ArtistApplicationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'type' => $this->faker->randomElement(['artist', 'creator']),
            'status' => $this->faker->randomElement(['sent', 'accepted', 'declined']),
            'followers' => $this->faker->numberBetween(0, 100000),
            'listeners' => $this->faker->numberBetween(0, 100000),
            'youtube' => $this->faker->url(),
            'tiktok' => $this->faker->url(),
            'instagram' => $this->faker->url(),
            'spotify' => $this->faker->url(),
            'twitch' => $this->faker->url(),
            'description' => $this->faker->sentence(),
            'admin_notes' => $this->faker->sentence(),
        ];
    }
}

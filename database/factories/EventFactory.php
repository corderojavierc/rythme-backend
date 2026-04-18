<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

final class EventFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'title' => $this->faker->sentence(4),
            'description' => $this->faker->paragraph(),
            'location' => $this->faker->address(),
            'date' => $this->faker->dateTimeBetween('now', '+1 year')->format('Y-m-d H:i:s'),
            'image' => $this->faker->optional()->imageUrl(800, 600, 'party'),
            'capacity' => (string) $this->faker->numberBetween(50, 10000),
        ];
    }
}

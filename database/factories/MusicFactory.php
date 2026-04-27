<?php

declare(strict_types=1);

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

final class MusicFactory extends Factory
{
    public function definition(): array
    {
        return [
            'id' => Str::uuid(),
            'title' => $this->faker->sentence(3),
            'artist' => $this->faker->name(),
            'cover_url' => $this->faker->imageUrl(640, 640, 'music'),
            'release_date' => $this->faker->date(),
        ];
    }
}

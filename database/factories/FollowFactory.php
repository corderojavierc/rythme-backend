<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

final class FollowFactory extends Factory
{
    public function definition(): array
    {
        return [
            'id' => Str::uuid(),
            'follower_id' => User::factory(),
            'followed_id' => User::factory(),
        ];
    }
}

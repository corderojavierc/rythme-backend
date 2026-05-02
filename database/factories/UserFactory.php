<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\UserTypeEnum;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends Factory<User>
 */
final class UserFactory extends Factory
{
    private static ?string $password = null;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'username' => fake()->unique()->userName(),
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'type' => UserTypeEnum::USER,
            'followers' => 0,
            'following' => 0,
            'posts' => 0,
            'password' => self::$password ??= Hash::make('password'),
            'remember_token' => Str::random(10),
            'profile_image' => $this->profilePic(),
        ];
    }

    public function unverified(): self
    {
        return $this->state(fn (array $attributes): array => [
            'email_verified_at' => null,
        ]);
    }

    public function profilePic(): string
    {
        return 'https://api.dicebear.com/9.x/thumbs/svg?seed='.fake()->name();
    }
}

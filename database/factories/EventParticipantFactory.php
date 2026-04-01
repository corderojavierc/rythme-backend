<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Event;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

final class EventParticipantFactory extends Factory
{
    public function definition(): array
    {
        return [
            'id' => Str::uuid(),
            'event_id' => Event::factory(),
            'user_id' => User::factory(),
        ];
    }
}

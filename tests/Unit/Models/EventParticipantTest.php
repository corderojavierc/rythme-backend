<?php

declare(strict_types=1);

use App\Models\EventParticipant;

test('to array', function (): void {
    $eventParticipant = EventParticipant::factory()->create()->refresh();

    expect(array_keys($eventParticipant->toArray()))
        ->toBe([
            'id',
            'event_id',
            'user_id',
            'created_at',
            'updated_at',
        ]);
});

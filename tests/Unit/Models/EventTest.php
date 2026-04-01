<?php

declare(strict_types=1);

use App\Models\Event;

test('to array', function (): void {
    $event = Event::factory()->create()->refresh();

    expect(array_keys($event->toArray()))
        ->toBe([
            'id',
            'user_id',
            'title',
            'description',
            'location',
            'date',
            'image',
            'created_at',
            'updated_at',
        ]);
});

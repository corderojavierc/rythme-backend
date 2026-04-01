<?php

declare(strict_types=1);

use App\Models\Recommendation;

test('to array', function (): void {
    $recommendation = Recommendation::factory()->create()->refresh();

    expect(array_keys($recommendation->toArray()))
        ->toBe([
            'id',
            'user_id',
            'music_id',
            'message',
            'created_at',
            'updated_at',
        ]);
});

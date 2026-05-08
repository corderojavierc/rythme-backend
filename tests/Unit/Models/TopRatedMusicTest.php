<?php

declare(strict_types=1);

use App\Models\TopRatedMusic;

test('to array', function (): void {
    $user = TopRatedMusic::factory()->create();

    expect(array_keys($user->toArray()))
        ->toBe([
            'period',
            'rank_position',
            'music_id',
            'avg_rating',
            'count_ratings',
            'updated_at',
            'created_at',
        ]);
});

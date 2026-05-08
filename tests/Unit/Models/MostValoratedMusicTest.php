<?php

declare(strict_types=1);

use App\Models\MostValoratedMusic;

test('to array', function (): void {
    $user = MostValoratedMusic::factory()->create();

    expect(array_keys($user->toArray()))
        ->toBe([
            'period',
            'rank_position',
            'music_id',
            'rating',
            'count_ratings',
            'updated_at',
            'created_at',
        ]);
});

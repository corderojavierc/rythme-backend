<?php

declare(strict_types=1);

use App\Models\Follow;

test('to array', function (): void {
    $follow = Follow::factory()->create()->refresh();

    expect(array_keys($follow->toArray()))
        ->toBe([
            'id',
            'follower_id',
            'followed_id',
            'created_at',
            'updated_at',
        ]);
});

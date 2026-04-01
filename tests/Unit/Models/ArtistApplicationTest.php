<?php

declare(strict_types=1);

use App\Models\ArtistApplication;

test('to array', function (): void {
    $artistApplication = ArtistApplication::factory()->create()->refresh();

    expect(array_keys($artistApplication->toArray()))
        ->toBe([
            'id',
            'user_id',
            'artist',
            'followers',
            'listeners',
            'youtube',
            'tiktok',
            'instagram',
            'spotify',
            'twitch',
            'description',
            'created_at',
            'updated_at',
        ]);
});

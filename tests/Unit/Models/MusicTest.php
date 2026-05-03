<?php

declare(strict_types=1);

use App\Models\Music;

test('to array', function (): void {
    $music = Music::factory()->create()->refresh();

    expect(array_keys($music->toArray()))
        ->toBe([
            'id',
            'title',
            'artist',
            'spotify_artist_ids',
            'cover_url',
            'release_date',
            'created_at',
            'updated_at',
        ]);
});

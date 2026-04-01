<?php

declare(strict_types=1);

use App\Models\Post;

test('to array', function (): void {
    $post = Post::factory()->create()->refresh();

    expect(array_keys($post->toArray()))
        ->toBe([
            'id',
            'user_id',
            'music_id',
            'text',
            'rating',
            'count_likes',
            'created_at',
            'updated_at',
        ]);
});

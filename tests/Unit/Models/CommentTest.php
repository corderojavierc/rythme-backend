<?php

declare(strict_types=1);

use App\Models\Comment;

test('to array', function (): void {
    $comment = Comment::factory()->create()->refresh();

    expect(array_keys($comment->toArray()))
        ->toBe([
            'id',
            'post_id',
            'user_id',
            'text',
            'count_likes',
            'created_at',
            'updated_at',
        ]);
});

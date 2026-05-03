<?php

declare(strict_types=1);

use App\Models\User;

test('to array', function (): void {
    $user = User::factory()->create()->refresh();

    expect(array_keys($user->toArray()))
        ->toBe([
            'id',
            'username',
            'name',
            'email',
            'email_verified_at',
            'type',
            'spotify_id',
            'followers',
            'following',
            'posts',
            'musics',
            'profile_image',
            'created_at',
            'updated_at',
        ]);
});

<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\ArtistApplication;
use App\Models\Comment;
use App\Models\Event;
use App\Models\Follow;
use App\Models\Music;
use App\Models\Post;
use App\Models\Recommendation;
use App\Models\User;
use Illuminate\Database\Seeder;

final class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        User::factory()->create([
            'username' => 'javi',
            'name' => 'javi',
            'second_name' => 'cordero',
            'email' => 'javi@gmail.com',
            'password' => 'javi123',
            'profile_image' => 'https://api.dicebear.com/9.x/thumbs/svg?seed=javi',
        ]);

        $users = User::factory(10)->create();
        $musics = Music::factory(30)->create();

        foreach ($users as $user) {
            /** @var User $user */
            if (random_int(1, 100) <= 30) {
                ArtistApplication::factory()->create([
                    'user_id' => $user->id,
                ]);
            }

            Event::factory(random_int(1, 3))->create([
                'user_id' => $user->id,
            ]);

            $randomMusicsForPosts = $musics->random(random_int(2, 5));
            foreach ($randomMusicsForPosts as $music) {
                /** @var Music $music */
                Post::factory()->create([
                    'user_id' => $user->id,
                    'music_id' => $music->id,
                ]);
            }

            $followedUsers = $users->where('id', '!=', $user->id)->random(random_int(1, 4));
            foreach ($followedUsers as $followed) {
                /** @var User $followed */
                Follow::factory()->create([
                    'follower_id' => $user->id,
                    'followed_id' => $followed->id,
                ]);
            }

            $randomMusicsForRecs = $musics->random(random_int(0, 3));
            foreach ($randomMusicsForRecs as $music) {
                /** @var Music $music */
                Recommendation::factory()->create([
                    'user_id' => $user->id,
                    'music_id' => $music->id,
                ]);
            }
        }

        $posts = Post::all();
        foreach ($posts as $post) {
            /** @var Post $post */
            /** @var User $randomUser */
            $randomUser = $users->random();

            Comment::factory(random_int(1, 4))->create([
                'post_id' => $post->id,
                'user_id' => $randomUser->id,
            ]);
        }
    }
}

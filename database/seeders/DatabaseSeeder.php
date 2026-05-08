<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Enums\UserTypeEnum;
use App\Models\Comment;
use App\Models\Follow;
use App\Models\Music;
use App\Models\Post;
use App\Models\User;
use Illuminate\Database\Seeder;

final class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        User::factory()->create([
            'username' => config('app.username', 'admin'),
            'name' => config('app.userfirstname', 'admin'),
            'email' => config('app.email', 'admin@admin.com'),
            'password' => config('app.password', 'admin'),
            'profile_image' => config('app.image', 'https://api.dicebear.com/9.x/thumbs/svg?seed=admin'),
            'type' => UserTypeEnum::ADMIN,
        ]);

        $this->command->info('Generando usuarios...');
        $users = User::factory(11)->create();

        $this->command->info('Generando canciones...');
        $musics = collect();
        for ($i = 0; $i < 40; $i++) {
            $music = Music::factory()->create();
            $musics->push($music);
        }

        $musics = $musics->unique('id')->values();

        $this->command->info('Generando posts...');
        foreach ($users as $user) {
            /** @var User $user */
            $randomMusicsForPosts = $musics->random(random_int(3, 6));
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
        }

        $this->command->info('Generando comentarios...');
        $posts = Post::all();
        foreach ($posts as $post) {
            /** @var Post $post */
            $commentCount = random_int(0, 3);

            if ($commentCount === 0) {
                continue;
            }

            $commenters = $users
                ->where('id', '!=', $post->user_id)
                ->take($commentCount);

            foreach ($commenters as $commenter) {
                /** @var User $commenter */
                Comment::factory()->create([
                    'post_id' => $post->id,
                    'user_id' => $commenter->id,
                ]);
            }
        }

        $this->command->info('Datos generados correctamente.');
    }
}

<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Comment;
use App\Models\MostValoratedMusic;
use App\Models\Music;
use App\Models\Post;
use App\Models\TopRatedMusic;
use App\Models\User;
use Carbon\CarbonInterface;
use Illuminate\Database\Seeder;

final class OldDataSeeder extends Seeder
{
    private const int MONTHS_BACK = 2;

    public function run(): void
    {
        $users = User::all();
        $musics = Music::all();

        if ($users->isEmpty() || $musics->isEmpty()) {
            $this->command->warn('Ejecuta primero el DatabaseSeeder.');

            return;
        }

        for ($i = self::MONTHS_BACK; $i >= 1; $i--) {
            $monthStart = now()->subMonths($i)->startOfMonth();
            $monthEnd = now()->subMonths($i)->endOfMonth();
            $period = $monthStart->toDateString();

            $this->command->info('Generando datos para: '.$monthStart->format('Y-m'));

            foreach ($users as $user) {
                $randomMusics = $musics->random(random_int(4, 10));

                foreach ($randomMusics as $music) {
                    if (Post::query()->where('user_id', $user->id)->where('music_id', $music->id)->exists()) {
                        continue;
                    }

                    $postDate = now()->createFromTimestamp(
                        random_int($monthStart->timestamp, $monthEnd->timestamp)
                    );

                    /** @var Post $post */
                    $post = Post::factory()->create([
                        'user_id' => $user->id,
                        'music_id' => $music->id,
                        'created_at' => $postDate,
                        'updated_at' => $postDate,
                    ]);

                    $users->where('id', '!=', $user->id)
                        ->shuffle()
                        ->take(random_int(4, 10))
                        ->each(function (User $commenter) use ($post, $postDate): void {
                            Comment::factory()->create([
                                'post_id' => $post->id,
                                'user_id' => $commenter->id,
                                'created_at' => $postDate,
                                'updated_at' => $postDate,
                            ]);
                        });
                }
            }

            $this->snapshotTopRated($period, $monthStart, $monthEnd);
            $this->snapshotMostRated($period, $monthStart, $monthEnd);
        }

        $this->command->info('Datos históricos generados correctamente.');
    }

    private function snapshotTopRated(string $period, CarbonInterface $monthStart, CarbonInterface $monthEnd): void
    {
        $rows = Post::query()
            ->whereBetween('created_at', [$monthStart, $monthEnd])
            ->selectRaw('music_id, AVG(rating) as rating, COUNT(*) as count_ratings')
            ->groupBy('music_id')
            ->orderByDesc('rating')
            ->limit(10)
            ->get()
            ->map(fn (Post $item, int $i): array => [
                'period' => $period,
                'rank_position' => $i + 1,
                'music_id' => $item->music_id,
                'rating' => round((float) $item->rating, 2),
                'count_ratings' => (int) $item->count_ratings,
            ])
            ->all();

        if (! empty($rows)) {
            TopRatedMusic::query()->upsert($rows, uniqueBy: ['period', 'rank_position'], update: ['music_id', 'rating', 'count_ratings']);
        }
    }

    private function snapshotMostRated(string $period, CarbonInterface $monthStart, CarbonInterface $monthEnd): void
    {
        $rows = Post::query()
            ->whereBetween('created_at', [$monthStart, $monthEnd])
            ->selectRaw('music_id, COUNT(*) as count_ratings, AVG(rating) as rating')
            ->groupBy('music_id')
            ->orderByDesc('count_ratings')
            ->orderByDesc('rating')
            ->limit(10)
            ->get()
            ->map(fn (Post $item, int $i): array => [
                'period' => $period,
                'rank_position' => $i + 1,
                'music_id' => $item->music_id,
                'count_ratings' => (int) $item->count_ratings,
                'rating' => round((float) $item->rating, 2),
            ])
            ->all();

        if (! empty($rows)) {
            MostValoratedMusic::query()->upsert($rows, uniqueBy: ['period', 'rank_position'], update: ['music_id', 'count_ratings', 'rating']);
        }
    }
}

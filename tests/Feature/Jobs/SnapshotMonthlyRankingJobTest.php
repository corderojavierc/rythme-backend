<?php

declare(strict_types=1);

use Aerni\Spotify\Facades\Spotify;
use App\Jobs\SnapshotMonthlyRankingJob;
use App\Models\MostValoratedMusic;
use App\Models\Music;
use App\Models\Post;
use App\Models\TopRatedMusic;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Queue;

beforeEach(function (): void {
    Spotify::shouldReceive('searchTracks->limit->get')
        ->zeroOrMoreTimes()
        ->andReturn(['tracks' => ['items' => []]]);
});

it('creates top rated music snapshot for the given month', function (): void {
    $month = now()->subMonth();
    $music = Music::factory()->create();

    Post::factory()->create([
        'music_id' => $music->id,
        'rating' => 5,
        'created_at' => $month->copy()->startOfMonth()->addDays(5),
    ]);
    Post::factory()->create([
        'music_id' => $music->id,
        'rating' => 4,
        'created_at' => $month->copy()->startOfMonth()->addDays(10),
    ]);

    new SnapshotMonthlyRankingJob($month)->handle();

    $this->assertDatabaseHas('top_rated_musics', [
        'music_id' => $music->id,
        'rank_position' => 1,
        'rating' => 4.5,
        'count_ratings' => 2,
    ]);
});

it('creates most valorated music snapshot for the given month', function (): void {
    $month = now()->subMonth();
    $music = Music::factory()->create();

    Post::factory()->create([
        'music_id' => $music->id,
        'rating' => 3,
        'created_at' => $month->copy()->startOfMonth()->addDays(2),
    ]);
    Post::factory()->create([
        'music_id' => $music->id,
        'rating' => 5,
        'created_at' => $month->copy()->startOfMonth()->addDays(8),
    ]);

    new SnapshotMonthlyRankingJob($month)->handle();

    $this->assertDatabaseHas('most_valorated_musics', [
        'music_id' => $music->id,
        'rank_position' => 1,
        'count_ratings' => 2,
    ]);
});

it('orders top rated snapshot by rating descending', function (): void {
    $month = now()->subMonth();

    $bestMusic = Music::factory()->create();
    $worstMusic = Music::factory()->create();

    Post::factory()->create([
        'music_id' => $bestMusic->id,
        'rating' => 5,
        'created_at' => $month->copy()->startOfMonth(),
    ]);
    Post::factory()->create([
        'music_id' => $worstMusic->id,
        'rating' => 2,
        'created_at' => $month->copy()->startOfMonth(),
    ]);

    new SnapshotMonthlyRankingJob($month)->handle();

    $this->assertDatabaseHas('top_rated_musics', ['music_id' => $bestMusic->id, 'rank_position' => 1]);
    $this->assertDatabaseHas('top_rated_musics', ['music_id' => $worstMusic->id, 'rank_position' => 2]);
});

it('orders most valorated snapshot by count descending', function (): void {
    $month = now()->subMonth();

    $popularMusic = Music::factory()->create();
    $unpopularMusic = Music::factory()->create();

    Post::factory(3)->create([
        'music_id' => $popularMusic->id,
        'created_at' => $month->copy()->startOfMonth(),
    ]);
    Post::factory()->create([
        'music_id' => $unpopularMusic->id,
        'created_at' => $month->copy()->startOfMonth(),
    ]);

    new SnapshotMonthlyRankingJob($month)->handle();

    $this->assertDatabaseHas('most_valorated_musics', ['music_id' => $popularMusic->id, 'rank_position' => 1]);
    $this->assertDatabaseHas('most_valorated_musics', ['music_id' => $unpopularMusic->id, 'rank_position' => 2]);
});

it('does not create records if there are no posts for the month', function (): void {
    $month = now()->subMonth();

    new SnapshotMonthlyRankingJob($month)->handle();

    expect(TopRatedMusic::query()->count())->toBe(0);
    expect(MostValoratedMusic::query()->count())->toBe(0);
});

it('only includes posts from the specified month', function (): void {
    $targetMonth = now()->subMonth();
    $otherMonth = now()->subMonths(2);

    $music = Music::factory()->create();

    Post::factory()->create([
        'music_id' => $music->id,
        'rating' => 5,
        'created_at' => $targetMonth->copy()->startOfMonth(),
    ]);

    Post::factory()->create([
        'music_id' => $music->id,
        'rating' => 1,
        'created_at' => $otherMonth->copy()->startOfMonth(),
    ]);

    new SnapshotMonthlyRankingJob($targetMonth)->handle();

    $this->assertDatabaseHas('top_rated_musics', [
        'music_id' => $music->id,
        'rating' => 5.0,
        'count_ratings' => 1,
    ]);
});

it('defaults to previous month when no month is provided', function (): void {
    $previousMonth = now()->subMonth();
    $music = Music::factory()->create();

    Post::factory()->create([
        'music_id' => $music->id,
        'rating' => 4,
        'created_at' => $previousMonth->copy()->startOfMonth()->addDays(3),
    ]);

    new SnapshotMonthlyRankingJob()->handle();

    $this->assertDatabaseHas('top_rated_musics', [
        'music_id' => $music->id,
        'rank_position' => 1,
    ]);
});

it('has a unique id based on the month', function (): void {
    $month = now()->subMonth();
    $job = new SnapshotMonthlyRankingJob($month);

    expect($job->uniqueId())->toBe($month->format('Y-m'));
});

it('clears cache after running', function (): void {
    $month = now()->subMonth();
    $cacheKey = $month->format('Y-m');

    Cache::put('rankings:top-rated:'.$cacheKey, 'old-data');
    Cache::put('rankings:most-rated:'.$cacheKey, 'old-data');
    Cache::put('rankings:top-rated:history:'.$cacheKey, 'old-data');
    Cache::put('rankings:most-rated:history:'.$cacheKey, 'old-data');

    new SnapshotMonthlyRankingJob($month)->handle();

    expect(Cache::has('rankings:top-rated:'.$cacheKey))->toBeFalse();
    expect(Cache::has('rankings:most-rated:'.$cacheKey))->toBeFalse();
    expect(Cache::has('rankings:top-rated:history:'.$cacheKey))->toBeFalse();
    expect(Cache::has('rankings:most-rated:history:'.$cacheKey))->toBeFalse();
});

it('can be dispatched to the queue', function (): void {
    Queue::fake();

    dispatch(new SnapshotMonthlyRankingJob(now()->subMonth()));

    Queue::assertPushed(SnapshotMonthlyRankingJob::class);
});

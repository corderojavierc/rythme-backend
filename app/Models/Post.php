<?php

declare(strict_types=1);

namespace App\Models;

use Carbon\CarbonInterface;
use Database\Factories\PostFactory;
use Illuminate\Database\Eloquent\Attributes\UseFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Override;

/**
 * @property string $id
 * @property string $user_id
 * @property string $music_id
 * @property string $text
 * @property float $rating
 * @property int $count_likes
 * @property int $count_comments
 * @property int $count_ratings
 * @property-read Music $music
 * @property-read CarbonInterface $created_at
 * @property-read CarbonInterface $updated_at
 */
#[UseFactory(PostFactory::class)]
final class Post extends Model
{
    use HasFactory;
    use HasUuids;

    #[Override]
    protected $table = 'posts';

    #[Override]
    protected $fillable = [
        'user_id',
        'music_id',
        'text',
        'rating',
        'count_likes',
        'count_comments',
    ];

    public static function booted(): void
    {
        self::created(function (Post $post): void {
            $post->updateMusicRatings();
            $post->user()->increment('posts');
        });

        self::deleted(function (Post $post): void {
            $post->updateMusicRatings();
            $post->user()->decrement('posts');
        });
    }

    public function casts(): array
    {
        return [
            'id' => 'string',
            'user_id' => 'string',
            'music_id' => 'string',
            'text' => 'string',
            'rating' => 'decimal:2',
            'count_likes' => 'integer',
            'count_comments' => 'integer',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function music(): BelongsTo
    {
        return $this->belongsTo(Music::class, 'music_id');
    }

    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class);
    }

    public function likes(): MorphMany
    {
        return $this->morphMany(Like::class, 'likeable');
    }

    private function updateMusicRatings(): void
    {
        $stats = self::query()
            ->where('music_id', $this->music_id)
            ->selectRaw('AVG(rating) as rating, COUNT(*) as total')
            ->first();

        MusicRating::query()->updateOrCreate(
            ['music_id' => $this->music_id],
            [
                'rating' => $stats->rating ?? 0,
                'count_ratings' => $stats->total ?? 0,
            ]
        );
    }
}

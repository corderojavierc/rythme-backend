<?php

declare(strict_types=1);

namespace App\Models;

use Database\Factories\PostFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Override;

/**
 * @property-read string $id
 * @property-read string $user_id
 * @property-read string $music_id
 * @property-read string $text
 * @property-read decimal $rating
 * @property-read int $likes
 * @property-read int $repost
 * @property-read CarbonInterface $created_at
 * @property-read CarbonInterface $updated_at
 */
final class Post extends Model
{
    /** @use HasFactory<PostFactory> */
    use HasFactory;

    use HasUuids;

    #[Override]
    protected $table = 'posts';

    /**
     * @return array<string, string>
     */
    public function casts(): array
    {
        return [
            'id' => 'string',
            'user_id' => 'string',
            'music_id' => 'string',
            'text' => 'string',
            'rating' => 'decimal:2',
            'count_likes' => 'integer',
            'count_repost' => 'integer',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function music(): BelongsTo
    {
        return $this->belongsTo(Music::class, 'music_id', 'id');
    }

    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class);
    }

    public function likes(): MorphMany
    {
        return $this->morphMany(Like::class, 'likeable');
    }
}

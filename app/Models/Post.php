<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use App\Models\User;
use App\Models\Comment;
use App\Models\Music;
use App\Models\Like;

/**
 * @property-read string $id
 * @property-read string $user_id
 * @property-read string $music_id
 * @property-read string $text
 * @property-read decimal $rating
 * @property-read integer $likes
 * @property-read integer $repost
 * @property-read CarbonInterface $created_at
 * @property-read CarbonInterface $updated_at
 */

class Post extends Model
{
    /** @use HasFactory<\Database\Factories\PostFactory> */
    use HasFactory;
    use HasUuids;

    protected $table = "posts";

    protected $fillable = [
        'user_id',
        'music_id',
        'text',
        'rating',
        'count_likes',
        'count_repost',
    ];

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

    public function user(): BelongsTo {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function music(): BelongsTo {
        return $this->belongsTo(Music::class, 'music_id', 'id');
    }

    public function comments(): HasMany{
        return $this->hasMany(Comment::class);
    }

    public function likes(): MorphMany{
        return $this->morphMany(Like::class, 'likeable');
    }
}

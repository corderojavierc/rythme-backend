<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use App\Models\User;
use App\Models\Post;
use App\Models\Like;

/**
 * @property-read string $id
 * @property-read string $post_id
 * @property-read string $user_id
 * @property-read string $text
 * @property-read integer $likes
 * @property-read integer $repost
 * @property-read CarbonInterface $created_at
 * @property-read CarbonInterface $updated_at
 *
 */

class Comment extends Model
{
    /** @use HasFactory<\Database\Factories\CommentFactory> */
    use HasFactory;

    use HasUuids;

    protected $table = "comments";

    protected $fillable = [
        'post_id',
        'user_id',
        'text',
        'likes',
        'repost',
    ];

    /**
     * @return array<string, string>
     */
    public function casts(): array
    {
        return [
            'id' => 'string',
            'post_id' => 'string',
            'user_id' => 'string',
            'text' => 'string',
            'count_likes' => 'integer',
            'count_repost' => 'integer',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo{
        return $this->belongsTo(User::class);
    }

    public function post(): BelongsTo{
        return $this->belongsTo(Post::class);
    }

    public function likes(): MorphMany{
        return $this->morphMany(Like::class, 'likeable');
    }
}

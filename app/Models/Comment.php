<?php

declare(strict_types=1);

namespace App\Models;

use Database\Factories\CommentFactory;
use Illuminate\Database\Eloquent\Attributes\UseFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Override;

#[UseFactory(CommentFactory::class)]
final class Comment extends Model
{
    use HasFactory;
    use HasUuids;

    #[Override]
    protected $table = 'comments';

    public static function booted(): void
    {
        self::created(function (Comment $comment): void {
            $comment->post()->increment('count_comments');
        });

        self::deleted(function (Comment $comment): void {
            $comment->post()->decrement('count_comments');
        });
    }

    public function casts(): array
    {
        return [
            'id' => 'string',
            'post_id' => 'string',
            'user_id' => 'string',
            'text' => 'string',
            'count_likes' => 'integer',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function post(): BelongsTo
    {
        return $this->belongsTo(Post::class);
    }

    public function likes(): MorphMany
    {
        return $this->morphMany(Like::class, 'likeable');
    }
}

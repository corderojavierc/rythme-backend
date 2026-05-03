<?php

declare(strict_types=1);

namespace App\Models;

use Carbon\CarbonInterface;
use Database\Factories\LikeFactory;
use Illuminate\Database\Eloquent\Attributes\UseFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Override;

/**
 * @property string $id
 * @property string $user_id
 * @property string $likeable_type
 * @property string $likeable_id
 * @property-read CarbonInterface $created_at
 * @property-read CarbonInterface $updated_at
 */
#[UseFactory(LikeFactory::class)]
final class Like extends Model
{
    use HasFactory;
    use HasUuids;

    #[Override]
    protected $table = 'likes';

    #[Override]
    protected $fillable = [
        'user_id',
        'likeable_type',
        'likeable_id',
    ];

    public static function booted(): void
    {
        self::created(function (Like $like): void {
            $like->likeable()->increment('count_likes');
        });

        self::deleted(function (Like $like): void {
            $like->likeable()->decrement('count_likes');
        });
    }

    public function casts(): array
    {
        return [
            'id' => 'string',
            'user_id' => 'string',
            'likeable_type' => 'string',
            'likeable_id' => 'string',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    public function likeable(): MorphTo
    {
        return $this->morphTo();
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}

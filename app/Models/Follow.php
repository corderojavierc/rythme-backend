<?php

declare(strict_types=1);

namespace App\Models;

use Carbon\CarbonInterface;
use Database\Factories\FollowFactory;
use Illuminate\Database\Eloquent\Attributes\UseFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\Pivot;
use Override;

/**
 * @property-read string $id
 * @property-read string $follower_id
 * @property-read string $followed_id
 * @property-read CarbonInterface $created_at
 * @property-read CarbonInterface $updated_at
 */
#[UseFactory(FollowFactory::class)]
final class Follow extends Pivot
{
    use HasFactory;
    use HasUuids;

    #[Override]
    protected $table = 'follows';

    public static function boot(): void
    {
        parent::boot();

        self::created(function (Follow $follow): void {
            $follow->followed()->increment('followers');
            $follow->follower()->increment('following');
        });

        self::deleted(function (Follow $follow): void {
            $follow->followed()->decrement('followers');
            $follow->follower()->decrement('following');
        });
    }

    /**
     * @return array<string, string>
     */
    public function casts(): array
    {
        return [
            'id' => 'string',
            'follower_id' => 'string',
            'followed_id' => 'string',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    public function follower(): BelongsTo
    {
        return $this->belongsTo(User::class, 'follower_id');
    }

    public function followed(): BelongsTo
    {
        return $this->belongsTo(User::class, 'followed_id');
    }
}

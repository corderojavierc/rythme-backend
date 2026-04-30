<?php

declare(strict_types=1);

namespace App\Models;

use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Override;

/**
 * @property-read string $id
 * @property-read string $user_id
 * @property-read string $type
 * @property-read string $status
 * @property-read int $followers
 * @property-read int | null $listeners
 * @property-read string | null $youtube
 * @property-read string | null $tiktok
 * @property-read string | null $instagram
 * @property-read string | null $spotify
 * @property-read string | null $twitch
 * @property-read string $description
 * @property-read string | null $admin_notes
 * @property-read CarbonInterface $created_at
 * @property-read CarbonInterface $updated_at
 */
final class ArtistApplication extends Model
{
    use HasFactory;
    use HasUuids;

    #[Override]
    protected $table = 'artist_applications';

    /**
     * @return array<string, string>
     */
    public function casts(): array
    {
        return [
            'id' => 'string',
            'user_id' => 'string',
            'type' => 'string',
            'status' => 'string',
            'followers' => 'integer',
            'listeners' => 'integer',
            'youtube' => 'string',
            'tiktok' => 'string',
            'instagram' => 'string',
            'spotify' => 'string',
            'twitch' => 'string',
            'description' => 'string',
            'admin_notes' => 'string',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}

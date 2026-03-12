<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;

/**
 * @property-read string $id
 * @property-read string $user_id
 * @property-read boolean $artist
 * @property-read integer $followers
 * @property-read integer | null $listeners
 * @property-read string | null $youtube
 * @property-read string | null $tiktok
 * @property-read string | null $instagram
 * @property-read string | null $spotify
 * @property-read string | null $twitch
 * @property-read string $description
 * @property-read CarbonInterface $created_at
 * @property-read CarbonInterface $updated_at
 */

class ArtistApplication extends Model
{
    /** @use HasFactory<\Database\Factories\ArtistApplicationFactory> */
    use HasFactory;
    use HasUuids;

    protected $fillable = [
        'user_id',
        'artist',
        'followers',
        'listeners',
        'youtube',
        'tiktok',
        'instagram',
        'spotify',
        'twitch',
        'description',
    ];

    /**
     * @return array<string, string>
     */
    public function casts(): array
    {
        return [
            'id' => 'string',
            'user_id' => 'string',
            'artist' => 'boolean',
            'followers' => 'integer',
            'listeners' => 'integer',
            'youtube' => 'string',
            'tiktok' => 'string',
            'instagram' => 'string',
            'spotify' => 'string',
            'twitch' => 'string',
            'description' => 'string',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo {
        return $this->belongsTo(User::class);
   }
}

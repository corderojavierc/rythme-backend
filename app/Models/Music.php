<?php

declare(strict_types=1);

namespace App\Models;

use Carbon\CarbonInterface;
use Database\Factories\MusicFactory;
use Illuminate\Database\Eloquent\Attributes\UseFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Override;

/**
 * @property-read string $id
 * @property-read string $title
 * @property-read string $artist
 * @property-read string $cover_url
 * @property-read string $description
 * @property-read string $release_date
 * @property-read CarbonInterface $created_at
 * @property-read CarbonInterface $updated_at
 */
#[UseFactory(MusicFactory::class)]
final class Music extends Model
{
    use HasFactory;
    use HasUuids;

    #[Override]
    protected $table = 'musics';

    public static function booted(): void
    {
        self::deleting(function (Music $music): void {
            foreach ($music->createdBy as $user) {
                $user->decrement('musics');
            }
        });
    }

    public function casts(): array
    {
        return [
            'id' => 'string',
            'title' => 'string',
            'artist' => 'string',
            'spotify_artist_ids' => 'array',
            'cover_url' => 'string',
            'release_date' => 'datetime',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    public function createdBy(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'music_user');
    }

    public function post(): HasMany
    {
        return $this->hasMany(Post::class);
    }

    public function recommendated(): BelongsToMany
    {
        return $this->belongsToMany(Recommendation::class, 'recommendations');
    }

    public function rating(): HasOne
    {
        return $this->hasOne(MusicRating::class);
    }

    public function topRatedHistory(): HasMany
    {
        return $this->hasMany(TopRatedMusic::class);
    }

    public function mostRatedHistory(): HasMany
    {
        return $this->hasMany(MostValoratedMusic::class);
    }
}

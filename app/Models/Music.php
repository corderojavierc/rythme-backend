<?php

declare(strict_types=1);

namespace App\Models;

use Database\Factories\MusicFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Override;

/**
 * @property-read string $id
 * @property-read string $title
 * @property-read string $cover_url
 * @property-read string $description
 * @property-read string $release_date
 * @property-read CarbonInterface $created_at
 * @property-read CarbonInterface $updated_at
 */
final class Music extends Model
{
    /** @use HasFactory<MusicFactory> */
    use HasFactory;

    use HasUuids;

    #[Override]
    protected $table = 'musics';

    /**
     * @return array<string, string>
     */
    public function casts(): array
    {
        return [
            'id' => 'string',
            'title' => 'string',
            'artist' => 'string',
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
}

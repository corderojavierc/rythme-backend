<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;
use Override;

/**
 * @property string $music_id
 * @property float $rating
 * @property int $count_ratings
 * @property-read Carbon $created_at
 * @property-read Carbon $updated_at
 */
final class MusicRating extends Model
{
    use HasFactory;
    use HasUuids;

    #[Override]
    public $incrementing = false;

    #[Override]
    protected $table = 'music_ratings';

    #[Override]
    protected $primaryKey = 'music_id';

    #[Override]
    protected $keyType = 'string';

    #[Override]
    protected $fillable = [
        'music_id',
        'rating',
        'count_ratings',
    ];

    public function casts(): array
    {
        return [
            'music_id' => 'string',
            'rating' => 'decimal:2',
            'count_ratings' => 'integer',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    public function music(): BelongsTo
    {
        return $this->belongsTo(Music::class, 'music_id');
    }
}

<?php

declare(strict_types=1);

namespace App\Models;

use Carbon\CarbonInterface;
use Database\Factories\TopRatedMusicFactory;
use Illuminate\Database\Eloquent\Attributes\UseFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Override;

/**
 * @property string $period
 * @property int $rank_position
 * @property string $music_id
 * @property float $rating
 * @property int $count_ratings
 * @property-read Music $music
 * @property CarbonInterface $created_at
 * @property CarbonInterface $updated_at
 */
#[UseFactory(TopRatedMusicFactory::class)]
final class TopRatedMusic extends Model
{
    use HasFactory;

    #[Override]
    public $incrementing = false;

    #[Override]
    protected $table = 'top_rated_musics';

    #[Override]
    protected $primaryKey = 'period';

    #[Override]
    protected $fillable = [
        'period',
        'rank_position',
        'music_id',
        'rating',
        'count_ratings',
    ];

    public function casts(): array
    {
        return [
            'rank_position' => 'integer',
            'rating' => 'decimal:2',
            'count_ratings' => 'integer',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    public function music(): BelongsTo
    {
        return $this->belongsTo(Music::class);
    }
}

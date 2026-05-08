<?php

declare(strict_types=1);

namespace App\Models;

use Carbon\CarbonInterface;
use Database\Factories\MostValoratedMusicFactory;
use Illuminate\Database\Eloquent\Attributes\UseFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Override;

/**
 * @property string $period
 * @property int $rank_position
 * @property string $music_id
 * @property int $count_ratings
 * @property float $rating
 * @property CarbonInterface $created_at
 * @property CarbonInterface $updated_at
 */
#[UseFactory(MostValoratedMusicFactory::class)]
final class MostValoratedMusic extends Model
{
    use HasFactory;

    #[Override]
    public $incrementing = false;

    #[Override]
    protected $table = 'most_valorated_musics';

    #[Override]
    protected $primaryKey = 'period';

    #[Override]
    protected $fillable = [
        'period',
        'rank_position',
        'music_id',
        'count_ratings',
        'rating',
    ];

    public function casts(): array
    {
        return [
            'rank_position' => 'integer',
            'count_ratings' => 'integer',
            'rating' => 'decimal:2',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    public function music(): BelongsTo
    {
        return $this->belongsTo(Music::class);
    }
}

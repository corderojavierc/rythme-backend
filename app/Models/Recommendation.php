<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

/**
 * @property-read string $id
 * @property-read string $user_id
 * @property-read string $music_id
 * @property-read string $message
 * @property-read CarbonInterface $created_at
 * @property-read CarbonInterface $updated_at
 */

class Recommendation extends Model
{
    /** @use HasFactory<\Database\Factories\RecommendationFactory> */
    use HasFactory;
    use HasUuids;

    protected $table = "recommendations";

    protected $fillable = [
        'post_id',
        'music_id',
        'message',
    ];

    /**
     * @return array<string, string>
     */
    public function casts(): array
    {
        return [
            'id' => 'string',
            'post_id' => 'string',
            'user_id' => 'string',
            'text' => 'string',
            'likes' => 'integer',
            'repost' => 'integer',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }
}

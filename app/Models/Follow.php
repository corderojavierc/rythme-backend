<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

/**
 * @property-read string $id
 * @property-read string $follower_id
 * @property-read string $followed_id
 * @property-read CarbonInterface $created_at
 * @property-read CarbonInterface $updated_at
 */

class Follow extends Model
{
    /** @use HasFactory<\Database\Factories\FollowFactory> */
    use HasFactory;

    use HasUuids;

    protected $table = 'follows';

    protected $fillable = [
        'follower_id',
        'followed_id',
    ];

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
}

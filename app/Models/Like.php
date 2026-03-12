<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;

/**
 * @property-read string $id
 * @property-read string $user_id
 * @property-read string $target_type
 * @property-read string $target_id
 * @property-read CarbonInterface $created_at
 * @property-read CarbonInterface $updated_at
 */

class Like extends Model
{
    /** @use HasFactory<\Database\Factories\LikeFactory> */
    use HasFactory;
    use HasUuids;

    protected $table = "likes";

    protected $fillable = [
        'user_id',
        'target_type',
        'target_id',
    ];

    /**
     * @return array<string, string>
     */
    public function casts(): array
    {
        return [
            'id' => 'string',
            'user_id' => 'string',
            'target_type' => 'string',
            'target_id' => 'string',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    public function target(): MorphTo{
        return $this->morphTo();
    }

    public function user(): BelongsTo{
        return $this->belongsTo(User::class);
    }
}

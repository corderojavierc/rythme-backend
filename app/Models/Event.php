<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;

/**
 * @property-read string $id
 * @property-read string $user_id
 * @property-read string $title
 * @property-read string $description
 * @property-read string $location
 * @property-read string $date
 * @property-read string | null $image
 * @property-read string $capacity
 * @property-read CarbonInterface $created_at
 * @property-read CarbonInterface $updated_at
 */

class Event extends Model
{
    /** @use HasFactory<\Database\Factories\EventFactory> */
    use HasFactory;

    protected $table = "events";

    protected $fillable = [
        'creator_id',
        'title',
        'description',
        'location',
        'date',
        'image',
        'capacity',
    ];

    /**
     * @return array<string, string>
     */
    public function casts(): array
    {
        return [
            'id' => 'string',
            'user_id' => 'string',
            'title' => 'string',
            'description' => 'string',
            'location' => 'string',
            'date' => 'string',
            'image' => 'string',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo{
        return $this->belongsTo(User::class);
    }

    public function hasParticipants(): BelongsToMany{
        return $this->belongsToMany(User::class, 'event_participants');
    }
}

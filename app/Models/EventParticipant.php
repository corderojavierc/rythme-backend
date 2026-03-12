<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

/**
 * @property-read string $id
 * @property-read string $event_id
 * @property-read string $user_id
 * @property-read CarbonInterface $created_at
 * @property-read CarbonInterface $updated_at
 */

class EventParticipant extends Model
{
    /** @use HasFactory<\Database\Factories\EventParticipantFactory> */
    use HasFactory;

    protected $fillable = [
        'event_id',
        'user_id',
    ];

    /**
     * @return array<string, string>
     */
    public function casts(): array
    {
        return [
            'id' => 'string',
            'event_id' => 'string',
            'user_id' => 'string',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }
}

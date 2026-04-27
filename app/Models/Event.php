<?php

declare(strict_types=1);

namespace App\Models;

use Carbon\CarbonInterface;
use Database\Factories\EventFactory;
use Illuminate\Database\Eloquent\Attributes\UseFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Override;

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
#[UseFactory(EventFactory::class)]
final class Event extends Model
{
    use HasFactory;
    use HasUuids;

    #[Override]
    protected $table = 'events';

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

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function hasParticipants(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'event_participants');
    }
}

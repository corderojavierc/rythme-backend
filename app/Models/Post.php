<?php

declare(strict_types=1);

namespace App\Models;

use Carbon\CarbonInterface;
use Database\Factories\PostFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Override;

final class Post extends Model
{
    use HasFactory;
    use HasUuids;

    protected $table = 'posts';

    public function casts(): array
    {
        return [
            'id' => 'string',
            'user_id' => 'string',
            'music_id' => 'string',
            'text' => 'string',
            'rating' => 'decimal:2',
            'count_likes' => 'integer',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function music(): BelongsTo
    {
        return $this->belongsTo(Music::class, 'music_id');
    }

    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class);
    }

    /**
     * ✅ Polimórfico correcto
     */
    public function likes(): MorphMany
    {
        return $this->morphMany(Like::class, 'likeable');
    }
}

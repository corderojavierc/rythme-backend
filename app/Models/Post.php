<?php

declare(strict_types=1);

namespace App\Models;

use Carbon\CarbonInterface;
use Database\Factories\PostFactory;
use Illuminate\Database\Eloquent\Attributes\UseFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Override;

/**
 * @property string $id
 * @property string $user_id
 * @property string $music_id
 * @property string $text
 * @property float $rating
 * @property int $count_likes
 * @property int $count_comments
 * @property int $count_ratings
 * @property-read Music $music
 * @property-read CarbonInterface $created_at
 * @property-read CarbonInterface $updated_at
 */
// Una reseña de canción hecha por un usuario. Al crearse/borrarse actualiza la nota media de la canción y el contador de posts del usuario
#[UseFactory(PostFactory::class)]
final class Post extends Model
{
    use HasFactory;
    use HasUuids;

    #[Override]
    protected $table = 'posts';

    #[Override]
    protected $fillable = [
        'user_id',
        'music_id',
        'text',
        'rating',
        'count_likes',
        'count_comments',
    ];

    // booted() es el hook de ciclo de vida del modelo en Laravel.
    // Aquí registramos callbacks que se ejecutan automáticamente al crear o borrar un post,
    // sin tener que acordarnos de llamarlos manualmente desde el controller.
    public static function booted(): void
    {
        self::created(function (Post $post): void {
            $post->updateMusicRatings();          // recalcula la nota media de la canción
            $post->user()->increment('posts');    // +1 al contador de posts del usuario
        });

        self::deleted(function (Post $post): void {
            $post->updateMusicRatings();          // recalcula la nota media sin este post
            $post->user()->decrement('posts');    // -1 al contador de posts del usuario
        });
    }

    public function casts(): array
    {
        return [
            'id' => 'string',
            'user_id' => 'string',
            'music_id' => 'string',
            'text' => 'string',
            'rating' => 'decimal:2',
            'count_likes' => 'integer',
            'count_comments' => 'integer',
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

    // morphMany: relación polimórfica. Un Like puede pertenecer a un Post o a un Comment.
    // 'likeable' es el nombre del morph: en la tabla likes hay columnas likeable_type y likeable_id.
    public function likes(): MorphMany
    {
        return $this->morphMany(Like::class, 'likeable');
    }

    // Recalcula y guarda la nota media y el total de reseñas de la canción asociada
    private function updateMusicRatings(): void
    {
        // Una sola query SQL calcula el promedio y el conteo de todos los posts de esta canción
        $stats = self::query()
            ->where('music_id', $this->music_id)
            ->selectRaw('AVG(rating) as rating, COUNT(*) as total')
            ->first();

        // updateOrCreate: si ya existe el MusicRating para esta canción lo actualiza,
        // si no existe lo crea. El primer array es la condición de búsqueda,
        // el segundo son los valores a guardar/actualizar.
        MusicRating::query()->updateOrCreate(
            ['music_id' => $this->music_id],
            [
                'rating' => $stats->rating ?? 0,       // si no hay posts, la media es 0
                'count_ratings' => $stats->total ?? 0,
            ]
        );
    }
}

<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Music;
use App\Services\SpotifyService;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Override;

/**
 * @extends Factory<Music>
 */
final class MusicFactory extends Factory
{
    private const array SEARCH_TERMS = [
        'love', 'night', 'summer', 'dream', 'fire',
        'heart', 'home', 'road', 'light', 'time',
    ];

    #[Override]
    protected $model = Music::class;

    public function definition(): array
    {
        return [
            'id' => Str::uuid(),
            'title' => $this->faker->sentence(3),
            'artist' => $this->faker->name(),
            'cover_url' => $this->faker->imageUrl(640, 640, 'music'),
            'release_date' => $this->faker->date(),
        ];
    }

    /**
     * @param  array<string, mixed>  $attributes
     */
    public function create($attributes = [], ?Model $parent = null): Music
    {
        $query = $this->faker->randomElement(self::SEARCH_TERMS);
        $music = SpotifyService::searchAndStore($query);

        if ($music instanceof Music) {
            return $music;
        }

        $model = parent::create($attributes, $parent);

        assert($model instanceof Music);

        return $model;
    }
}

<?php

declare(strict_types=1);

namespace App\Models;

use Carbon\CarbonInterface;
use Database\Factories\UserFactory;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Override;

/**
 * @property-read string $id
 * @property-read string $username
 * @property-read string $name
 * @property-read string $second_name
 * @property-read string $email
 * @property-read CarbonInterface|null $email_verified_at
 * @property-read int $is_verified_as
 * @property-read string $password
 * @property-read string|null $remember_token
 * @property-read string $profile_name
 * @property-read CarbonInterface $created_at
 * @property-read CarbonInterface $updated_at
 */
final class User extends Authenticatable implements MustVerifyEmail
{
    /** @use HasFactory<UserFactory> */
    use HasFactory;

    use HasUuids;
    use Notifiable;

    #[Override]
    protected $table = 'users';

    #[Override]
    protected $fillable = [
        'username',
        'name',
        'second_name',
        'email',
        'password',
    ];

    /**
     * @var list<string>
     */
    #[Override]
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * @return array<string, string>
     */
    public function casts(): array
    {
        return [
            'id' => 'string',
            'username' => 'string',
            'name' => 'string',
            'second_name' => 'string',
            'email' => 'string',
            'email_verified_at' => 'datetime',
            'is_verified_as' => 'string',
            'password' => 'hashed',
            'remember_token' => 'string',
            'profile_image' => 'string',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    public function followers(): BelongsToMany
    {
        return $this->belongsToMany(self::class, 'follows', 'followed_id', 'follower_id');
    }

    public function following(): BelongsToMany
    {
        return $this->belongsToMany(self::class, 'follows', 'follower_id', 'followed_id');
    }

    public function posts(): HasMany
    {
        return $this->hasMany(Post::class);
    }

    public function comment(): HasMany
    {
        return $this->hasMany(Comment::class);
    }

    public function gustar(): MorphToMany
    {
        return $this->morphToMany(Like::class, 'likeable');
    }

    public function createdMusic(): BelongsToMany
    {
        return $this->belongsToMany(Music::class, 'music_user');
    }

    public function recommendate(): BelongsToMany
    {
        return $this->belongsToMany(Music::class, 'recommendations');
    }

    public function createEvent(): HasMany
    {
        return $this->hasMany(Event::class);
    }

    public function participating(): HasMany
    {
        return $this->hasMany(Event::class, 'event_participants');
    }

    public function applicate(): HasMany
    {
        return $this->hasMany(ArtistApplication::class);
    }
}

/*

 */

<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\UserTypeEnum;
use Carbon\CarbonInterface;
use Database\Factories\UserFactory;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Attributes\UseFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Auth;
use Laravel\Sanctum\HasApiTokens;
use Override;

/**
 * @property-read string $id
 * @property-read string $username
 * @property-read string $name
 * @property-read string $email
 * @property-read CarbonInterface|null $email_verified_at
 * @property-read string $password
 * @property UserTypeEnum $type
 * @property-read int $followers
 * @property-read int $following
 * @property-read int $posts
 * @property-read string|null $remember_token
 * @property-read string $profile_image
 * @property-read CarbonInterface $created_at
 * @property-read CarbonInterface $updated_at
 * @property bool|null $is_following_auth
 */
// Modelo principal de usuario. Soporta roles (user, artist, creator, admin) y acceso al panel Filament
#[UseFactory(UserFactory::class)]
final class User extends Authenticatable implements FilamentUser, MustVerifyEmail
{
    use HasApiTokens;
    use HasFactory;
    use HasUuids;
    use Notifiable;

    #[Override]
    protected $table = 'users';

    #[Override]
    protected $hidden = [
        'password',
        'remember_token',
    ];

    public function casts(): array
    {
        return [
            'id' => 'string',
            'username' => 'string',
            'name' => 'string',
            'email' => 'string',
            'email_verified_at' => 'datetime',
            'is_verified_as' => 'string',
            'password' => 'hashed',
            'type' => UserTypeEnum::class,
            'spotify_id' => 'string',
            'followers' => 'integer',
            'following' => 'integer',
            'posts' => 'integer',
            'musics' => 'integer',
            'remember_token' => 'string',
            'profile_image' => 'string',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    public function isAdmin(): bool
    {
        return $this->type === UserTypeEnum::ADMIN;
    }

    public function isArtist(): bool
    {
        return $this->type === UserTypeEnum::ARTIST;
    }

    public function isCreator(): bool
    {
        return $this->type === UserTypeEnum::CREATOR;
    }

    public function isUser(): bool
    {
        return $this->type === UserTypeEnum::USER;
    }

    // Solo los admins pueden entrar al panel de Filament
    public function canAccessPanel(Panel $panel): bool
    {
        if ($this->type !== UserTypeEnum::ADMIN) {
            Auth::logout();
            abort(403, 'You dont have permission to access this section.');
        }

        return true;
    }

    // Usuarios que siguen a este usuario.
    // La tabla pivote 'follows' tiene: follower_id (quien sigue) y followed_id (quien es seguido).
    // BelongsToMany con self::class indica que la relación es User → User (la misma tabla).
    public function followers(): BelongsToMany
    {
        return $this->belongsToMany(self::class, 'follows', 'followed_id', 'follower_id');
    }

    // Usuarios a los que este usuario sigue (lo contrario de followers, misma tabla pivote)
    public function following(): BelongsToMany
    {
        return $this->belongsToMany(self::class, 'follows', 'follower_id', 'followed_id');
    }

    public function posts(): HasMany
    {
        return $this->hasMany(Post::class);
    }

    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class);
    }

    public function likes(): HasMany
    {
        return $this->hasMany(Like::class);
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

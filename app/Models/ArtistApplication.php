<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\ArtistApplicationStatusEnum;
use App\Enums\UserTypeEnum;
use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Override;

/**
 * @property string $id
 * @property string $user_id
 * @property UserTypeEnum $type
 * @property ArtistApplicationStatusEnum $status
 * @property int $followers
 * @property int|null $listeners
 * @property string|null $youtube
 * @property string|null $tiktok
 * @property string|null $instagram
 * @property string|null $spotify
 * @property string|null $twitch
 * @property string $description
 * @property string|null $admin_notes
 * @property-read CarbonInterface $created_at
 * @property-read CarbonInterface $updated_at
 */
final class ArtistApplication extends Model
{
    use HasFactory;
    use HasUuids;

    #[Override]
    protected $table = 'artist_applications';

    public function acceptApplication(string $id, string $adminNotes): bool
    {
        $application = $this->findOrFail($id);
        /** @var User $user */
        $user = User::query()->findOrFail($application->user_id);
        $application->status = ArtistApplicationStatusEnum::ACCEPTED;
        $application->admin_notes = $adminNotes;
        $application->save();
        if ($application->type === UserTypeEnum::ARTIST) {
            $user->type = UserTypeEnum::ARTIST;
        } elseif ($application->type === UserTypeEnum::CREATOR) {
            $user->type = UserTypeEnum::CREATOR;
        }

        $user->save();

        return true;
    }

    public function declineApplication(string $id, string $adminNotes): bool
    {
        $application = $this->findOrFail($id);
        $application->status = ArtistApplicationStatusEnum::DECLINED;
        $application->admin_notes = $adminNotes;
        $application->save();

        return true;
    }

    #[Override]
    public function casts(): array
    {
        return [
            'type' => UserTypeEnum::class,
            'status' => ArtistApplicationStatusEnum::class,
            'followers' => 'integer',
            'listeners' => 'integer',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}

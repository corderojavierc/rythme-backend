<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\MusicRatings\Pages;

use App\Filament\Admin\Resources\MusicRatings\MusicRatingResource;
use Filament\Resources\Pages\CreateRecord;
use Override;

final class CreateMusicRating extends CreateRecord
{
    #[Override]
    protected static string $resource = MusicRatingResource::class;
}

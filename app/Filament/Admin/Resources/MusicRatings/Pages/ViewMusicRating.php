<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\MusicRatings\Pages;

use App\Filament\Admin\Resources\MusicRatings\MusicRatingResource;
use Filament\Resources\Pages\ViewRecord;
use Override;

final class ViewMusicRating extends ViewRecord
{
    #[Override]
    protected static string $resource = MusicRatingResource::class;

    protected function getHeaderActions(): array
    {
        return [
        ];
    }
}

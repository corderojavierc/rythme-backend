<?php

namespace App\Filament\Admin\Resources\MusicRatings\Pages;

use App\Filament\Admin\Resources\MusicRatings\MusicRatingResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewMusicRating extends ViewRecord
{
    protected static string $resource = MusicRatingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}

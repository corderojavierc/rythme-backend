<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\MusicRatings\Pages;

use App\Filament\Admin\Resources\MusicRatings\MusicRatingResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;
use Override;

final class EditMusicRating extends EditRecord
{
    #[Override]
    protected static string $resource = MusicRatingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}

<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\MusicRatings\Pages;

use App\Filament\Admin\Resources\MusicRatings\MusicRatingResource;
use Filament\Resources\Pages\ListRecords;
use Override;

final class ListMusicRatings extends ListRecords
{
    #[Override]
    protected static string $resource = MusicRatingResource::class;

    protected function getHeaderActions(): array
    {
        return [
        ];
    }
}

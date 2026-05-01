<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\ArtistApplications\Pages;

use App\Filament\Admin\Resources\ArtistApplications\ArtistApplicationResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Override;

final class ListArtistApplications extends ListRecords
{
    #[Override]
    protected static string $resource = ArtistApplicationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}

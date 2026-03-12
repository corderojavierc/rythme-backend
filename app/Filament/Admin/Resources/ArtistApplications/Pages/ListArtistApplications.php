<?php

namespace App\Filament\Admin\Resources\ArtistApplications\Pages;

use App\Filament\Admin\Resources\ArtistApplications\ArtistApplicationResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListArtistApplications extends ListRecords
{
    protected static string $resource = ArtistApplicationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}

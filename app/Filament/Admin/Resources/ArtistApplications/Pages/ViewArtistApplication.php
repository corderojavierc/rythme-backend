<?php

namespace App\Filament\Admin\Resources\ArtistApplications\Pages;

use App\Filament\Admin\Resources\ArtistApplications\ArtistApplicationResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewArtistApplication extends ViewRecord
{
    protected static string $resource = ArtistApplicationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}

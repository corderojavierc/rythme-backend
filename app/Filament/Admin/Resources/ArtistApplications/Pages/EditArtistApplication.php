<?php

namespace App\Filament\Admin\Resources\ArtistApplications\Pages;

use App\Filament\Admin\Resources\ArtistApplications\ArtistApplicationResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditArtistApplication extends EditRecord
{
    protected static string $resource = ArtistApplicationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}

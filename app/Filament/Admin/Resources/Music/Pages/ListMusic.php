<?php

namespace App\Filament\Admin\Resources\Music\Pages;

use App\Filament\Admin\Resources\Music\MusicResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListMusic extends ListRecords
{
    protected static string $resource = MusicResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}

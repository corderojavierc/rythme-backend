<?php

namespace App\Filament\Admin\Resources\Likes\Pages;

use App\Filament\Admin\Resources\Likes\LikeResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListLikes extends ListRecords
{
    protected static string $resource = LikeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}

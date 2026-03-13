<?php

namespace App\Filament\Admin\Resources\Likes\Pages;

use App\Filament\Admin\Resources\Likes\LikeResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\ViewRecord;

class ViewLike extends ViewRecord
{
    protected static string $resource = LikeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}

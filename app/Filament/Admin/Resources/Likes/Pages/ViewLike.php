<?php

namespace App\Filament\Admin\Resources\Likes\Pages;

use App\Filament\Admin\Resources\Likes\LikeResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewLike extends ViewRecord
{
    protected static string $resource = LikeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}

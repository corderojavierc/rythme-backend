<?php

namespace App\Filament\Admin\Resources\Likes\Pages;

use App\Filament\Admin\Resources\Likes\LikeResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditLike extends EditRecord
{
    protected static string $resource = LikeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}

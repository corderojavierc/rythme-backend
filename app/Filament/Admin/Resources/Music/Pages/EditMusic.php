<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\Music\Pages;

use App\Filament\Admin\Resources\Music\MusicResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Override;

final class EditMusic extends EditRecord
{
    #[Override]
    protected static string $resource = MusicResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}

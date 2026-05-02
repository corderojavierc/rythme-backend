<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\Music\Pages;

use App\Filament\Admin\Resources\Music\MusicResource;
use Filament\Resources\Pages\ListRecords;
use Override;

final class ListMusic extends ListRecords
{
    #[Override]
    protected static string $resource = MusicResource::class;

    protected function getHeaderActions(): array
    {
        return [
        ];
    }
}

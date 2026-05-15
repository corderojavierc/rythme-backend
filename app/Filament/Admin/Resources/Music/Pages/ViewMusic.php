<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\Music\Pages;

use App\Filament\Admin\Resources\Music\MusicResource;
use Filament\Resources\Pages\ViewRecord;
use Override;

final class ViewMusic extends ViewRecord
{
    #[Override]
    protected static string $resource = MusicResource::class;

    protected function getHeaderActions(): array
    {
        return [
        ];
    }
}

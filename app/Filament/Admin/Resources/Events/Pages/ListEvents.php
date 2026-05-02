<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\Events\Pages;

use App\Filament\Admin\Resources\Events\EventResource;
use Filament\Resources\Pages\ListRecords;
use Override;

final class ListEvents extends ListRecords
{
    #[Override]
    protected static string $resource = EventResource::class;

    protected function getHeaderActions(): array
    {
        return [
        ];
    }
}

<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\Events\Pages;

use App\Filament\Admin\Resources\Events\EventResource;
use Filament\Resources\Pages\CreateRecord;
use Override;

final class CreateEvent extends CreateRecord
{
    #[Override]
    protected static string $resource = EventResource::class;
}

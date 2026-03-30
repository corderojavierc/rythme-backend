<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\Music\Pages;

use App\Filament\Admin\Resources\Music\MusicResource;
use Filament\Resources\Pages\CreateRecord;
use Override;

final class CreateMusic extends CreateRecord
{
    #[Override]
    protected static string $resource = MusicResource::class;
}

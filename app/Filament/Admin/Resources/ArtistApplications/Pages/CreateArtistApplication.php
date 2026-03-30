<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\ArtistApplications\Pages;

use App\Filament\Admin\Resources\ArtistApplications\ArtistApplicationResource;
use Filament\Resources\Pages\CreateRecord;
use Override;

final class CreateArtistApplication extends CreateRecord
{
    #[Override]
    protected static string $resource = ArtistApplicationResource::class;
}

<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\Likes\Pages;

use App\Filament\Admin\Resources\Likes\LikeResource;
use Filament\Resources\Pages\CreateRecord;
use Override;

final class CreateLike extends CreateRecord
{
    #[Override]
    protected static string $resource = LikeResource::class;
}

<?php

namespace App\Filament\Admin\Resources\Likes\Pages;

use App\Filament\Admin\Resources\Likes\LikeResource;
use Filament\Resources\Pages\CreateRecord;

class CreateLike extends CreateRecord
{
    protected static string $resource = LikeResource::class;
}

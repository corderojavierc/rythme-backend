<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\Comments\Pages;

use App\Filament\Admin\Resources\Comments\CommentResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\ViewRecord;
use Override;

final class ViewComment extends ViewRecord
{
    #[Override]
    protected static string $resource = CommentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}

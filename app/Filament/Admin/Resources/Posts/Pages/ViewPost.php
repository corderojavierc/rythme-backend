<?php
namespace App\Filament\Admin\Resources\Posts\Pages;

use App\Filament\Admin\Resources\Posts\PostResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\ViewRecord;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Infolists\Components\TextEntry;

class ViewPost extends ViewRecord
{
    protected static string $resource = PostResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}

<?php
namespace App\Filament\Admin\Resources\Likes\Schemas;

use App\Filament\Admin\Resources\Posts\PostResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\ViewRecord;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Infolists\Components\TextEntry;


class LikeInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Info')->columns(3)->schema([
                    TextEntry::make('user.username')->label('User')->icon('heroicon-o-user'),
                    TextEntry::make('likeable_type')->label('Target Type'),
                    TextEntry::make('likeable_id')->label('Target ID'),
                ]),
            ]);
    }
}

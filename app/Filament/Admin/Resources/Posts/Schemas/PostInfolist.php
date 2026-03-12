<?php

namespace App\Filament\Admin\Resources\Posts\Schemas;

use App\Filament\Admin\Resources\Posts\PostResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\ViewRecord;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Infolists\Components\TextEntry;

class PostInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Información del Post')->columns(3)->schema([
                    TextEntry::make('user.username')->label('Usuario')->icon('heroicon-o-user'),
                    TextEntry::make('music.title')->label('Música')->icon('heroicon-o-musical-note'),
                    TextEntry::make('rating')->label('Rating')->icon('heroicon-o-star')->suffix('/5'),
                ]),
                Section::make('Contenido')->schema([
                    TextEntry::make('text')->label('Texto')->columnSpanFull()->prose(),
                ]),
                Section::make('Estadísticas')->columns(2)->schema([
                    TextEntry::make('count_likes')->label('Likes')->icon('heroicon-o-heart'),
                    TextEntry::make('count_repost')->label('Reposts')->icon('heroicon-o-arrow-path'),
                ]),
            ]);
    }
}

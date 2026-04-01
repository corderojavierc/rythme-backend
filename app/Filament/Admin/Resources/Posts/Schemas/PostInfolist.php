<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\Posts\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

final class PostInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Info')->columns(3)->schema([
                    TextEntry::make('user.username')->label('User')->icon('heroicon-o-user'),
                    TextEntry::make('music.title')->label('Music')->icon('heroicon-o-musical-note'),
                    TextEntry::make('rating')->label('Rating')->icon('heroicon-o-star')->suffix('/5'),
                ]),
                Section::make('Comment')->schema([
                    TextEntry::make('text')->label('Text')->columnSpanFull()->prose(),
                ]),
                Section::make('Stats')->columns(2)->schema([
                    TextEntry::make('count_likes')->label('Likes')->icon('heroicon-o-heart'),
                ]),
            ]);
    }
}

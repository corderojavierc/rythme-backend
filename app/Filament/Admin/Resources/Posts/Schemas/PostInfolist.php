<?php

namespace App\Filament\Admin\Resources\Posts\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class PostInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('id')
                    ->label('ID'),
                TextEntry::make('user.name')
                    ->label('User'),
                TextEntry::make('music.title')
                    ->label('Music'),
                TextEntry::make('text'),
                TextEntry::make('rating')
                    ->numeric(),
                TextEntry::make('count_likes')
                    ->numeric(),
                TextEntry::make('count_comments')
                    ->numeric(),
                TextEntry::make('created_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('updated_at')
                    ->dateTime()
                    ->placeholder('-'),
            ]);
    }
}

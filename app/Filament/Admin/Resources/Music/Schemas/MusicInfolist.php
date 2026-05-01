<?php

namespace App\Filament\Admin\Resources\Music\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class MusicInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('id')
                    ->label('ID'),
                TextEntry::make('title'),
                TextEntry::make('artist'),
                TextEntry::make('cover_url'),
                TextEntry::make('release_date'),
                TextEntry::make('created_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('updated_at')
                    ->dateTime()
                    ->placeholder('-'),
            ]);
    }
}

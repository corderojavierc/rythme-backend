<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\MusicRatings\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

final class MusicRatingInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('music.title')
                    ->label('Music'),
                TextEntry::make('rating')
                    ->numeric(),
                TextEntry::make('count_ratings')
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

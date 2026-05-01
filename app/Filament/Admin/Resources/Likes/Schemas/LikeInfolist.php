<?php

namespace App\Filament\Admin\Resources\Likes\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class LikeInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('id')
                    ->label('ID'),
                TextEntry::make('user.name')
                    ->label('User'),
                TextEntry::make('likeable_type'),
                TextEntry::make('likeable_id'),
                TextEntry::make('created_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('updated_at')
                    ->dateTime()
                    ->placeholder('-'),
            ]);
    }
}

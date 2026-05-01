<?php

namespace App\Filament\Admin\Resources\MusicRatings\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class MusicRatingForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('rating')
                    ->required()
                    ->numeric()
                    ->default(0),
                TextInput::make('count_ratings')
                    ->required()
                    ->numeric()
                    ->default(0),
            ]);
    }
}

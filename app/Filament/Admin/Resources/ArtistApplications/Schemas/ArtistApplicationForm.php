<?php

namespace App\Filament\Admin\Resources\ArtistApplications\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class ArtistApplicationForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('user_id')
                    ->relationship('user', 'username')
                    ->required(),
                Toggle::make('artist')
                    ->required(),
                TextInput::make('followers')
                    ->required()
                    ->numeric(),
                TextInput::make('listeners')
                    ->numeric(),
                TextInput::make('youtube'),
                TextInput::make('tiktok'),
                TextInput::make('instagram'),
                TextInput::make('spotify'),
                TextInput::make('twitch'),
                TextInput::make('description')
                    ->required(),
            ]);
    }
}

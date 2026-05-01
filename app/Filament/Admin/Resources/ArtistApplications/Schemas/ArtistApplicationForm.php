<?php

namespace App\Filament\Admin\Resources\ArtistApplications\Schemas;

use App\Enums\ArtistApplicationStatusEnum;
use App\Enums\UserTypeEnum;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class ArtistApplicationForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('user_id')
                    ->relationship('user', 'name')
                    ->required(),
                Select::make('type')
                    ->options(UserTypeEnum::class)
                    ->required(),
                Select::make('status')
                    ->options(ArtistApplicationStatusEnum::class)
                    ->default('sent')
                    ->required(),
                TextInput::make('followers')
                    ->required()
                    ->numeric()
                    ->default(0),
                TextInput::make('listeners')
                    ->numeric(),
                TextInput::make('youtube'),
                TextInput::make('tiktok'),
                TextInput::make('instagram'),
                TextInput::make('spotify'),
                TextInput::make('twitch'),
                Textarea::make('description')
                    ->required()
                    ->columnSpanFull(),
                Textarea::make('admin_notes')
                    ->columnSpanFull(),
            ]);
    }
}

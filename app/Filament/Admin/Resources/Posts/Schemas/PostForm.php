<?php

namespace App\Filament\Admin\Resources\Posts\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class PostForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('user_id')
                    ->relationship('user', 'username')
                    ->required(),
                Select::make('music_id')
                    ->relationship('music', 'title')
                    ->required(),
                TextInput::make('text')
                    ->required(),
                TextInput::make('rating')
                    ->required()
                    ->numeric(),
                TextInput::make('likes')
                    ->required()
                    ->numeric(),
                TextInput::make('repost')
                    ->required()
                    ->numeric(),
            ]);
    }
}

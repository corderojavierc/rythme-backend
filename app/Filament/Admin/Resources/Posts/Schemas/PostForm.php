<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\Posts\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

final class PostForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('user_id')
                    ->relationship('user', 'name')
                    ->required(),
                Select::make('music_id')
                    ->relationship('music', 'title')
                    ->required(),
                TextInput::make('text')
                    ->required(),
                TextInput::make('rating')
                    ->required()
                    ->numeric(),
                TextInput::make('count_likes')
                    ->required()
                    ->numeric()
                    ->default(0),
                TextInput::make('count_comments')
                    ->required()
                    ->numeric()
                    ->default(0),
            ]);
    }
}

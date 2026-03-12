<?php

namespace App\Filament\Admin\Resources\Comments\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class CommentForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('post_id')
                    ->relationship('post', 'id')
                    ->required(),
                TextInput::make('user_id')
                    ->required(),
                TextInput::make('text')
                    ->required(),
                TextInput::make('likes')
                    ->required()
                    ->numeric(),
                TextInput::make('repost')
                    ->required()
                    ->numeric(),
            ]);
    }
}

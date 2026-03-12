<?php

namespace App\Filament\Admin\Resources\Likes\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class LikeForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('user_id')
                    ->relationship('user', 'username')
                    ->required(),
                TextInput::make('target_type')
                    ->required(),
                TextInput::make('target_id')
                    ->required(),
            ]);
    }
}

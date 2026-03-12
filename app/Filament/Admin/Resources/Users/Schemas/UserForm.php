<?php

namespace App\Filament\Admin\Resources\Users\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('username')
                    ->label('Username'),
                TextInput::make('name'),
                TextInput::make('second_name'),
                TextInput::make('email')
                    ->label('Email address')
                    ->email(),
                DateTimePicker::make('email_verified_at'),
                Select::make('is_verified_as')
                    ->options([
                        0 => 'User',
                        1 => 'Creator',
                        2 => 'Artist',
                    ])
                    ->default(0)
                    ->required(),
                TextInput::make('password')
                    ->password(),
                FileUpload::make('profile_image')
                    ->image(),
            ]);
    }
}

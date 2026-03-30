<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\Users\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

final class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('username')
                    ->disabledOn('edit'),
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
                    ->password()
                    ->dehydrated(fn ($state): bool => filled($state)),
                FileUpload::make('profile_image')
                    ->image()
                    ->dehydrated(fn ($state): bool => filled($state)),
            ]);
    }
}

<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\Users\Schemas;

use App\Enums\UserTypeEnum;
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
                    ->required(),
                TextInput::make('name')
                    ->required(),
                TextInput::make('second_name')
                    ->required(),
                TextInput::make('email')
                    ->label('Email address')
                    ->email()
                    ->required(),
                DateTimePicker::make('email_verified_at'),
                Select::make('type')
                    ->options(UserTypeEnum::class)
                    ->default('user')
                    ->required(),
                TextInput::make('followers')
                    ->required()
                    ->numeric()
                    ->default(0),
                TextInput::make('following')
                    ->required()
                    ->numeric()
                    ->default(0),
                TextInput::make('posts')
                    ->required()
                    ->numeric()
                    ->default(0),
                TextInput::make('password')
                    ->password()
                    ->required(),
                FileUpload::make('profile_image')
                    ->image()
                    ->required(),
            ]);
    }
}

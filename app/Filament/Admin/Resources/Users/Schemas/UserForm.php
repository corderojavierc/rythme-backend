<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\Users\Schemas;

use App\Enums\UserTypeEnum;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Facades\Hash;

final class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Group::make()
                    ->schema([
                        Section::make('Personal Information')
                            ->description('Basic user details and access credentials.')
                            ->icon(Heroicon::User)
                            ->schema([
                                TextInput::make('name')
                                    ->required()
                                    ->maxLength(255),

                                TextInput::make('second_name')
                                    ->label('Last name')
                                    ->required()
                                    ->maxLength(255),

                                TextInput::make('username')
                                    ->prefix('@')
                                    ->required()
                                    ->maxLength(255)
                                    ->unique(ignoreRecord: true),

                                TextInput::make('email')
                                    ->label('Email address')
                                    ->email()
                                    ->required()
                                    ->maxLength(255)
                                    ->unique(ignoreRecord: true),

                                TextInput::make('password')
                                    ->password()
                                    ->required(fn (string $operation): bool => $operation === 'create')
                                    ->dehydrateStateUsing(fn (string $state): string => Hash::make($state))
                                    ->dehydrated(fn (?string $state): bool => filled($state))
                                    ->maxLength(255)
                                    ->columnSpan('full'),
                            ])
                            ->columns(2),

                        Section::make('Profile Image')
                            ->schema([
                                FileUpload::make('profile_image')
                                    ->hiddenLabel()
                                    ->image()
                                    ->imageEditor()
                                    ->circleCropper()
                                    ->directory('users/avatars')
                                    ->required(),
                            ])
                            ->collapsible(),
                    ])
                    ->columnSpan(['lg' => 2]),

                Group::make()
                    ->schema([
                        Section::make('Account Status')
                            ->schema([
                                Select::make('type')
                                    ->options(UserTypeEnum::class)
                                    ->default('user')
                                    ->native(false)
                                    ->required(),

                                DateTimePicker::make('email_verified_at')
                                    ->label('Email verified at')
                                    ->native(false),
                            ]),

                        Section::make('Statistics')
                            ->description('Current user metrics.')
                            ->schema([
                                TextInput::make('followers')
                                    ->required()
                                    ->numeric()
                                    ->default(0)
                                    ->prefixIcon(Heroicon::Users),

                                TextInput::make('following')
                                    ->required()
                                    ->numeric()
                                    ->default(0)
                                    ->prefixIcon(Heroicon::UserPlus),

                                TextInput::make('posts')
                                    ->required()
                                    ->numeric()
                                    ->default(0)
                                    ->prefixIcon(Heroicon::DocumentText),
                            ]),
                    ])
                    ->columnSpan(['lg' => 1]),
            ])
            ->columns(3);
    }
}

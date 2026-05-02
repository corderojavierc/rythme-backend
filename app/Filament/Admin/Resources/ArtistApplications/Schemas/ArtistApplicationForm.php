<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\ArtistApplications\Schemas;

use App\Enums\ArtistApplicationStatusEnum;
use App\Enums\UserTypeEnum;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;

final class ArtistApplicationForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Group::make()
                    ->schema([
                        Section::make('Application Content')
                            ->icon(Heroicon::DocumentText)
                            ->schema([
                                Textarea::make('description')
                                    ->label('Artist Bio / Motivation')
                                    ->required()
                                    ->rows(6)
                                    ->columnSpanFull(),

                                Textarea::make('admin_notes')
                                    ->label('Internal Admin Notes')
                                    ->rows(4)
                                    ->columnSpanFull(),
                            ]),

                        Section::make('Social Presence')
                            ->icon(Heroicon::GlobeAlt)
                            ->schema([
                                TextInput::make('spotify')
                                    ->prefixIcon(Heroicon::Link)
                                    ->url(),
                                TextInput::make('youtube')
                                    ->prefixIcon(Heroicon::Link)
                                    ->url(),
                                TextInput::make('instagram')
                                    ->prefixIcon(Heroicon::Camera)
                                    ->url(),
                                TextInput::make('tiktok')
                                    ->prefixIcon(Heroicon::VideoCamera)
                                    ->url(),
                                TextInput::make('twitch')
                                    ->prefixIcon(Heroicon::ComputerDesktop)
                                    ->url(),
                            ])
                            ->columns(2),
                    ])
                    ->columnSpan(['lg' => 2]),

                Group::make()
                    ->schema([
                        Section::make('Status & Type')
                            ->schema([
                                Select::make('status')
                                    ->options(ArtistApplicationStatusEnum::class)
                                    ->default('sent')
                                    ->native(false)
                                    ->required(),

                                Select::make('type')
                                    ->options(UserTypeEnum::class)
                                    ->native(false)
                                    ->required(),

                                Select::make('user_id')
                                    ->relationship('user', 'username')
                                    ->searchable()
                                    ->preload()
                                    ->required(),
                            ]),

                        Section::make('Metrics')
                            ->schema([
                                TextInput::make('followers')
                                    ->numeric()
                                    ->default(0)
                                    ->prefixIcon(Heroicon::Users)
                                    ->required(),

                                TextInput::make('listeners')
                                    ->numeric()
                                    ->prefixIcon(Heroicon::MusicalNote),
                            ]),
                    ])
                    ->columnSpan(['lg' => 1]),
            ])
            ->columns(3);
    }
}

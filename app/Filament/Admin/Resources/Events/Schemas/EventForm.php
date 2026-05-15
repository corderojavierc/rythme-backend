<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\Events\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;

final class EventForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Group::make()
                    ->schema([
                        Section::make('Event Details')
                            ->icon(Heroicon::Ticket)
                            ->schema([
                                TextInput::make('title')
                                    ->required()
                                    ->maxLength(255)
                                    ->columnSpanFull(),

                                Textarea::make('description')
                                    ->rows(4)
                                    ->required()
                                    ->columnSpanFull(),

                                TextInput::make('location')
                                    ->prefixIcon(Heroicon::MapPin)
                                    ->required()
                                    ->columnSpanFull(),
                            ])
                            ->columns(2),

                        Section::make('Promotional Image')
                            ->schema([
                                FileUpload::make('image')
                                    ->image()
                                    ->imageEditor()
                                    ->directory('events/covers')
                                    ->hiddenLabel(),
                            ])
                            ->collapsible(),
                    ])
                    ->columnSpan(['lg' => 2]),
                Group::make()
                    ->schema([
                        Section::make('Organization')
                            ->schema([
                                Select::make('user_id')
                                    ->relationship('user', 'username')
                                    ->searchable()
                                    ->preload()
                                    ->native(false)
                                    ->required(),

                                DateTimePicker::make('date')
                                    ->native(false)
                                    ->required(),

                                TextInput::make('capacity')
                                    ->numeric()
                                    ->minValue(1)
                                    ->prefixIcon(Heroicon::Users)
                                    ->required(),
                            ]),
                    ])
                    ->columnSpan(['lg' => 1]),
            ])
            ->columns(3);
    }
}

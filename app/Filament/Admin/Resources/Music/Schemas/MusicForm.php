<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\Music\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;

final class MusicForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Group::make()
                    ->schema([
                        Section::make('Music Information')
                            ->icon(Heroicon::MusicalNote)
                            ->schema([
                                TextInput::make('title')
                                    ->required()
                                    ->maxLength(255),

                                TextInput::make('artist')
                                    ->required()
                                    ->maxLength(255),
                            ])
                            ->columns(2),

                        Section::make('Album Art')
                            ->schema([
                                FileUpload::make('cover_image')
                                    ->image()
                                    ->imageEditor()
                                    ->directory('music/covers')
                                    ->hiddenLabel(),
                            ])
                            ->collapsible(),
                    ])
                    ->columnSpan(['lg' => 2]),
                Group::make()
                    ->schema([
                        Section::make('Release Details')
                            ->schema([
                                DatePicker::make('release_date')
                                    ->native(false)
                                    ->required(),

                                TextInput::make('cover_url')
                                    ->label('External Cover URL')
                                    ->url()
                                    ->maxLength(255),
                            ]),
                    ])
                    ->columnSpan(['lg' => 1]),
            ])
            ->columns(3);
    }
}

<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\MusicRatings\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;

final class MusicRatingForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Group::make()
                    ->schema([
                        Section::make('Music Association')
                            ->schema([
                                Select::make('music_id')
                                    ->relationship('music', 'title')
                                    ->searchable()
                                    ->preload()
                                    ->native(false)
                                    ->required(),
                            ]),
                    ])
                    ->columnSpan(['lg' => 1]),

                Group::make()
                    ->schema([
                        Section::make('Rating Metrics')
                            ->description('Aggregate performance of the track.')
                            ->icon(Heroicon::Star)
                            ->schema([
                                TextInput::make('rating')
                                    ->required()
                                    ->numeric()
                                    ->default(0)
                                    ->step(0.01)
                                    ->minValue(0)
                                    ->maxValue(5)
                                    ->prefixIcon(Heroicon::Star),

                                TextInput::make('count_ratings')
                                    ->label('Total Ratings')
                                    ->required()
                                    ->numeric()
                                    ->default(0)
                                    ->prefixIcon(Heroicon::ChartBar),
                            ])
                            ->columns(2),
                    ])
                    ->columnSpan(['lg' => 2]),
            ])
            ->columns(3);
    }
}

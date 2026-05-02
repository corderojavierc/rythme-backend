<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\MusicRatings\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Enums\FontWeight;
use Filament\Support\Enums\TextSize;
use Filament\Support\Icons\Heroicon;

final class MusicRatingInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Group::make([
                    Section::make('Music Information')
                        ->icon(Heroicon::MusicalNote)
                        ->schema([
                            TextEntry::make('music.title')
                                ->label('Track Title')
                                ->weight(FontWeight::Bold)
                                ->size(TextSize::Large)
                                ->color('primary')
                                ->icon(Heroicon::MusicalNote),

                            TextEntry::make('music.artist')
                                ->label('Artist')
                                ->color('gray'),
                        ]),
                ])
                    ->columnSpan(['lg' => 2]),
                Group::make([
                    Section::make('Statistics')
                        ->schema([
                            TextEntry::make('rating')
                                ->numeric()
                                ->badge()
                                ->color(fn (mixed $state): string => match (true) {
                                    $state >= 4 => 'success',
                                    $state >= 2 => 'warning',
                                    default => 'danger',
                                })
                                ->icon(Heroicon::Star),

                            TextEntry::make('count_ratings')
                                ->label('Total Ratings')
                                ->numeric()
                                ->icon(Heroicon::ChartBarSquare),
                        ]),

                    Section::make('Timestamps')
                        ->schema([
                            TextEntry::make('created_at')
                                ->label('Calculated At')
                                ->dateTime('d/m/Y H:i')
                                ->icon(Heroicon::Calendar),

                            TextEntry::make('updated_at')
                                ->label('Last Update')
                                ->dateTime('d/m/Y H:i')
                                ->icon(Heroicon::Clock)
                                ->color('gray'),
                        ]),
                ])
                    ->columnSpan(['lg' => 1]),
            ])
            ->columns(3);
    }
}

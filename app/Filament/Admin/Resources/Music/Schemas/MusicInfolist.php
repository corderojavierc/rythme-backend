<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\Music\Schemas;

use App\Models\Music;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Enums\FontWeight;
use Filament\Support\Enums\TextSize;
use Filament\Support\Icons\Heroicon;

final class MusicInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Group::make([
                    Section::make('Track Details')
                        ->icon(Heroicon::MusicalNote)
                        ->schema([
                            Group::make([
                                ImageEntry::make('cover_image')
                                    ->hiddenLabel()
                                    ->circular()
                                    ->defaultImageUrl(fn (Music $record): string => $record->cover_url ?? '')
                                    ->columnSpan(1),

                                Group::make([
                                    TextEntry::make('title')
                                        ->weight(FontWeight::Bold)
                                        ->size(TextSize::Large)
                                        ->color('primary'),

                                    TextEntry::make('artist')
                                        ->size(TextSize::Medium)
                                        ->icon(Heroicon::Microphone),
                                ])
                                    ->columnSpan(3),
                            ])
                                ->columns(4),
                        ]),
                ])
                    ->columnSpan(['lg' => 2]),

                Group::make([
                    Section::make('Music Metadata')
                        ->schema([
                            TextEntry::make('release_date')
                                ->date()
                                ->icon(Heroicon::CalendarDays),

                            TextEntry::make('cover_url')
                                ->label('Source URL')
                                ->limit(30)
                                ->copyable()
                                ->icon(Heroicon::Link)
                                ->color('gray'),
                        ]),

                    Section::make('Timestamps')
                        ->schema([
                            TextEntry::make('created_at')
                                ->label('Created')
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

<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\Events\Schemas;

use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Enums\FontWeight;
use Filament\Support\Enums\TextSize;
use Filament\Support\Icons\Heroicon;

final class EventInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Group::make([
                    Section::make('Event Information')
                        ->icon(Heroicon::Ticket)
                        ->schema([
                            TextEntry::make('title')
                                ->size(TextSize::Large)
                                ->weight(FontWeight::Bold)
                                ->columnSpanFull(),

                            TextEntry::make('description')
                                ->prose()
                                ->columnSpanFull(),

                            TextEntry::make('location')
                                ->icon(Heroicon::MapPin)
                                ->color('gray')
                                ->columnSpanFull(),
                        ]),

                    Section::make('Visuals')
                        ->schema([
                            ImageEntry::make('image')
                                ->hiddenLabel()
                                ->extraImgAttributes(['class' => 'rounded-xl w-full max-h-64 object-cover'])
                                ->placeholder('No cover image uploaded'),
                        ]),
                ])
                    ->columnSpan(['lg' => 2]),
                Group::make([
                    Section::make('Logistics')
                        ->schema([
                            TextEntry::make('user.username')
                                ->label('Organizer')
                                ->prefix('@')
                                ->weight(FontWeight::Bold)
                                ->color('primary'),

                            TextEntry::make('date')
                                ->dateTime('d/m/Y H:i')
                                ->icon(Heroicon::CalendarDays),

                            TextEntry::make('capacity')
                                ->numeric()
                                ->icon(Heroicon::UserGroup),
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

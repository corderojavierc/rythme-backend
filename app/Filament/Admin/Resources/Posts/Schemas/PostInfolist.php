<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\Posts\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Enums\FontWeight;
use Filament\Support\Enums\TextSize;
use Filament\Support\Icons\Heroicon;

final class PostInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Group::make([
                    Section::make('Post Content')
                        ->icon(Heroicon::DocumentText)
                        ->schema([
                            TextEntry::make('text')
                                ->hiddenLabel()
                                ->size(TextSize::Large)
                                ->prose()
                                ->columnSpan('full'),
                        ]),

                    Section::make('Statistics')
                        ->icon(Heroicon::ChartBar)
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

                            TextEntry::make('count_likes')
                                ->label('Likes')
                                ->numeric()
                                ->icon(Heroicon::Heart)
                                ->weight(FontWeight::Bold),

                            TextEntry::make('count_comments')
                                ->label('Comments')
                                ->numeric()
                                ->icon(Heroicon::ChatBubbleLeft)
                                ->weight(FontWeight::Bold),
                        ])
                        ->columns(3),
                ])
                    ->columnSpan(['lg' => 2]),

                Group::make([
                    Section::make('Associations')
                        ->schema([
                            TextEntry::make('user.username')
                                ->label('Author')
                                ->prefix('@')
                                ->icon(Heroicon::User)
                                ->color('primary')
                                ->weight(FontWeight::Bold),

                            TextEntry::make('music.title')
                                ->label('Music Track')
                                ->icon(Heroicon::MusicalNote)
                                ->color('info'),
                        ]),

                    Section::make('Timestamps')
                        ->schema([
                            TextEntry::make('created_at')
                                ->label('Published')
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

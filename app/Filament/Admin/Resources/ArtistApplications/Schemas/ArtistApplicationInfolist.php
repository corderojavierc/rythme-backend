<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\ArtistApplications\Schemas;

use App\Services\SpotifyService;
use BackedEnum;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Enums\FontWeight;
use Filament\Support\Icons\Heroicon;

final class ArtistApplicationInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Group::make([
                    Section::make('Review Status')
                        ->schema([
                            TextEntry::make('status')
                                ->badge()
                                ->color(fn (string|BackedEnum $state): string => match ($state instanceof BackedEnum ? $state->value : $state) {
                                    'approved' => 'success',
                                    'rejected' => 'danger',
                                    'pending' => 'warning',
                                    default => 'gray',
                                }),

                            TextEntry::make('type')
                                ->badge()
                                ->color('info'),

                            TextEntry::make('user.username')
                                ->label('Applicant')
                                ->prefix('@')
                                ->weight(FontWeight::Bold)
                                ->color('primary'),
                        ])
                        ->columns(3),
                    Section::make('Social Links')
                        ->columns(2)
                        ->schema([
                            TextEntry::make('spotify')
                                ->label('Spotify Artist')
                                ->icon(Heroicon::Link)
                                ->color('primary')
                                ->hidden(fn (string $state): bool => ! $state)
                                ->formatStateUsing(function (string $state): ?string {
                                    if ($state === '' || $state === '0') {
                                        return null;
                                    }

                                    $cleanId = $state;

                                    if (preg_match('/artist\/([a-zA-Z0-9]+)/', $state, $matches)) {
                                        $cleanId = $matches[1];
                                    }

                                    return SpotifyService::getArtistName($cleanId) ?? $state;
                                })
                                ->url(fn (mixed $state): string => 'https://open.spotify.com/intl-es/artist/'.$state)
                                ->openUrlInNewTab(),
                            TextEntry::make('youtube')->icon(Heroicon::Play)->url(fn (mixed $state): string => (string) $state)->openUrlInNewTab(),
                            TextEntry::make('instagram')->icon(Heroicon::Camera)->url(fn (mixed $state): string => (string) $state)->openUrlInNewTab(),
                            TextEntry::make('tiktok')->icon(Heroicon::VideoCamera)->url(fn (mixed $state): string => (string) $state)->openUrlInNewTab(),
                            TextEntry::make('twitch')->icon(Heroicon::ComputerDesktop)->url(fn (mixed $state): string => (string) $state)->openUrlInNewTab(),
                        ]),
                    Section::make('Proposal')
                        ->icon(Heroicon::ChatBubbleBottomCenterText)
                        ->schema([
                            TextEntry::make('description')
                                ->hiddenLabel()
                                ->prose()
                                ->columnSpanFull(),
                        ]),

                    Section::make('Admin Notes')
                        ->icon(Heroicon::ClipboardDocument)
                        ->schema([
                            TextEntry::make('admin_notes')
                                ->hiddenLabel()
                                ->prose()
                                ->columnSpanFull(),
                        ]),
                ])
                    ->columnSpan(['lg' => 2]),

                Group::make([
                    Section::make('Impact Metrics')
                        ->columns(2)
                        ->schema([
                            TextEntry::make('followers')
                                ->numeric()
                                ->icon(Heroicon::Users),

                            TextEntry::make('listeners')
                                ->numeric()
                                ->icon(Heroicon::MusicalNote),
                        ]),
                    Section::make('Timestamps')
                        ->schema([
                            TextEntry::make('created_at')
                                ->dateTime('d/m/Y H:i')
                                ->icon(Heroicon::Calendar),

                            TextEntry::make('updated_at')
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

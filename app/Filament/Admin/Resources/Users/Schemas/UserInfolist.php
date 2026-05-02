<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\Users\Schemas;

use App\Models\User;
use BackedEnum;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Enums\FontWeight;
use Filament\Support\Enums\TextSize;
use Filament\Support\Icons\Heroicon;

final class UserInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Group::make([
                    Section::make('Profile Information')
                        ->icon(Heroicon::UserCircle)
                        ->schema([
                            Group::make([
                                ImageEntry::make('profile_image')
                                    ->hiddenLabel()
                                    ->circular()
                                    ->defaultImageUrl(fn (User $record): string => 'https://api.dicebear.com/9.x/thumbs/svg?seed='.urlencode((string) $record->username))
                                    ->columnSpan(['default' => 4, 'sm' => 1]),

                                Group::make([
                                    TextEntry::make('name')
                                        ->label('Full Name')
                                        ->formatStateUsing(fn (User $record): string => mb_trim(sprintf('%s %s', $record->name, $record->second_name)))
                                        ->weight(FontWeight::Bold)
                                        ->size(TextSize::Large),

                                    TextEntry::make('username')
                                        ->icon(Heroicon::AtSymbol)
                                        ->color('gray'),

                                    TextEntry::make('email')
                                        ->icon(Heroicon::Envelope)
                                        ->copyable()
                                        ->copyMessage('Email copied')
                                        ->copyMessageDuration(1500),
                                ])
                                    ->columns(2)
                                    ->columnSpan(['default' => 4, 'sm' => 3]),
                            ])->columns(4),
                        ]),

                    Section::make('Metrics')
                        ->schema([
                            TextEntry::make('followers')
                                ->numeric()
                                ->icon(Heroicon::Users),

                            TextEntry::make('following')
                                ->numeric()
                                ->icon(Heroicon::UserPlus),

                            TextEntry::make('posts')
                                ->numeric()
                                ->icon(Heroicon::DocumentText),
                        ])
                        ->columns(3),
                ])
                    ->columnSpan(['lg' => 2]),

                Group::make([
                    Section::make('Account Status')
                        ->schema([
                            TextEntry::make('type')
                                ->badge()
                                ->color(fn (string|BackedEnum $state): string => match ($state instanceof BackedEnum ? $state->value : $state) {
                                    'admin' => 'danger',
                                    'mod' => 'warning',
                                    default => 'gray',
                                }),

                            TextEntry::make('email_verified_at')
                                ->label('Verified')
                                ->badge()
                                ->color(fn (mixed $state): string => $state ? 'success' : 'danger')
                                ->formatStateUsing(fn (mixed $state): string => $state ? 'Yes' : 'No')
                                ->icon(fn (mixed $state): Heroicon => $state ? Heroicon::CheckBadge : Heroicon::XCircle),
                        ]),

                    Section::make('Timestamps')
                        ->schema([
                            TextEntry::make('created_at')
                                ->label('Registered At')
                                ->dateTime('d/m/Y H:i')
                                ->icon(Heroicon::Calendar),

                            TextEntry::make('updated_at')
                                ->label('Last Updated')
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

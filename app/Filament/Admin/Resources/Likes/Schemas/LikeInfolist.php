<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\Likes\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Enums\FontWeight;
use Filament\Support\Icons\Heroicon;

final class LikeInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Group::make([
                    Section::make('Like Details')
                        ->icon(Heroicon::Heart)
                        ->schema([
                            TextEntry::make('likeable_type')
                                ->label('Target Type')
                                ->badge()
                                ->formatStateUsing(fn (string $state): string => str_replace('App\\Models\\', '', $state)),

                            TextEntry::make('likeable_id')
                                ->label('Target ID')
                                ->fontFamily('mono')
                                ->copyable()
                                ->color('gray'),
                        ])
                        ->columns(2),
                ])
                    ->columnSpan(['lg' => 2]),
                Group::make([
                    Section::make('Associations')
                        ->schema([
                            TextEntry::make('user.username')
                                ->label('User')
                                ->prefix('@')
                                ->weight(FontWeight::Bold)
                                ->icon(Heroicon::User)
                                ->color('primary'),
                        ]),

                    Section::make('Timestamps')
                        ->schema([
                            TextEntry::make('created_at')
                                ->label('Liked At')
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

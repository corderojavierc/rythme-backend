<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\Comments\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Enums\FontWeight;
use Filament\Support\Icons\Heroicon;

final class CommentInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([

                Group::make([
                    Section::make('Content')
                        ->icon(Heroicon::ChatBubbleBottomCenter)
                        ->schema([
                            TextEntry::make('text')
                                ->hiddenLabel()
                                ->prose()
                                ->columnSpanFull(),
                        ]),

                    Section::make('Statistics')
                        ->schema([
                            TextEntry::make('count_likes')
                                ->label('Likes Received')
                                ->numeric()
                                ->icon(Heroicon::Heart),
                        ]),
                ])
                    ->columnSpan(['lg' => 2]),
                Group::make([
                    Section::make('Associations')
                        ->schema([
                            TextEntry::make('user.username')
                                ->label('Author')
                                ->prefix('@')
                                ->weight(FontWeight::Bold)
                                ->color('primary')
                                ->icon(Heroicon::User),

                            TextEntry::make('post.id')
                                ->label('Target Post ID')
                                ->fontFamily('mono')
                                ->icon(Heroicon::DocumentText),
                        ]),

                    Section::make('Timestamps')
                        ->schema([
                            TextEntry::make('created_at')
                                ->label('Posted At')
                                ->dateTime('d/m/Y H:i')
                                ->icon(Heroicon::Calendar),

                            TextEntry::make('updated_at')
                                ->label('Last Edit')
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

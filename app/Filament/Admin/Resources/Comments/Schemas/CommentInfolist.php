<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\Comments\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

final class CommentInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Info')->columns(3)->schema([
                    TextEntry::make('user.username')->label('User')->icon('heroicon-o-user'),
                    TextEntry::make('post.text')->label('Post')->icon('heroicon-o-chat-bubble-left'),
                ]),
                Section::make('Content')->schema([
                    TextEntry::make('text')->label('Text')->columnSpanFull()->prose(),
                ]),
                Section::make('Stats')->columns(2)->schema([
                    TextEntry::make('count_likes')->label('Likes')->icon('heroicon-o-heart'),
                    TextEntry::make('count_repost')->label('Reposts')->icon('heroicon-o-arrow-path'),
                ]),
            ]);
    }
}

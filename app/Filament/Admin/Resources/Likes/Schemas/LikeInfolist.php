<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\Likes\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

final class LikeInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Info')->columns(3)->schema([
                    TextEntry::make('user.username')->label('User')->icon('heroicon-o-user'),
                    TextEntry::make('likeable_type')->label('Target Type'),
                    TextEntry::make('likeable_id')->label('Target ID'),
                ]),
            ]);
    }
}

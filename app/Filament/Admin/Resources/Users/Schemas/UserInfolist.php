<?php

namespace App\Filament\Admin\Resources\Users\Schemas;

use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class UserInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('id')
                    ->label('ID'),
                TextEntry::make('username'),
                TextEntry::make('name'),
                TextEntry::make('second_name'),
                TextEntry::make('email')
                    ->label('Email address'),
                TextEntry::make('email_verified_at')
                    ->dateTime(),
                TextEntry::make('type')
                    ->badge(),
                TextEntry::make('followers')
                    ->numeric(),
                TextEntry::make('following')
                    ->numeric(),
                TextEntry::make('posts')
                    ->numeric(),
                ImageEntry::make('profile_image'),
                TextEntry::make('created_at')
                    ->dateTime(),
                TextEntry::make('updated_at')
                    ->dateTime(),
            ]);
    }
}

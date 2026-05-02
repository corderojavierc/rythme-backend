<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\Likes\Schemas;

use App\Models\Post;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;

final class LikeForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Group::make()
                    ->schema([
                        Section::make('Polymorphic Target')
                            ->icon(Heroicon::Heart)
                            ->schema([
                                TextInput::make('likeable_type')
                                    ->label('Resource Type')
                                    ->placeholder(Post::class)
                                    ->required(),

                                TextInput::make('likeable_id')
                                    ->label('Resource ID')
                                    ->placeholder('UUID')
                                    ->required(),
                            ])
                            ->columns(2),
                    ])
                    ->columnSpan(['lg' => 2]),
                Group::make()
                    ->schema([
                        Section::make('Owner')
                            ->schema([
                                Select::make('user_id')
                                    ->relationship('user', 'username')
                                    ->searchable()
                                    ->preload()
                                    ->native(false)
                                    ->required(),
                            ]),
                    ])
                    ->columnSpan(['lg' => 1]),

            ])
            ->columns(3);
    }
}

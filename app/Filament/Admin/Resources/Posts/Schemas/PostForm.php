<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\Posts\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;

final class PostForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Group::make()
                    ->schema([
                        Section::make('Post Content')
                            ->icon(Heroicon::DocumentText)
                            ->schema([
                                Textarea::make('text')
                                    ->label('Content')
                                    ->required()
                                    ->rows(6)
                                    ->maxLength(65535)
                                    ->columnSpan('full'),

                                TextInput::make('rating')
                                    ->required()
                                    ->numeric()
                                    ->minValue(0)
                                    ->maxValue(5)
                                    ->step(0.1)
                                    ->prefixIcon(Heroicon::Star)
                                    ->columnSpan('full'),
                            ])
                            ->columns(1),
                    ])
                    ->columnSpan(['lg' => 2]),
                Group::make()
                    ->schema([
                        Section::make('Associations')
                            ->schema([
                                Select::make('user_id')
                                    ->relationship('user', 'username')
                                    ->searchable()
                                    ->preload()
                                    ->native(false)
                                    ->required(),

                                Select::make('music_id')
                                    ->relationship('music', 'title')
                                    ->searchable()
                                    ->preload()
                                    ->native(false)
                                    ->required(),
                            ]),

                        Section::make('Statistics')
                            ->schema([
                                TextInput::make('count_likes')
                                    ->label('Likes')
                                    ->required()
                                    ->numeric()
                                    ->default(0)
                                    ->prefixIcon(Heroicon::Heart),

                                TextInput::make('count_comments')
                                    ->label('Comments')
                                    ->required()
                                    ->numeric()
                                    ->default(0)
                                    ->prefixIcon(Heroicon::ChatBubbleLeft),
                            ]),
                    ])
                    ->columnSpan(['lg' => 1]),
            ])
            ->columns(3);
    }
}

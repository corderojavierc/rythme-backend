<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\Comments\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;

final class CommentForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([

                Group::make()
                    ->schema([
                        Section::make('Comment Content')
                            ->icon(Heroicon::ChatBubbleBottomCenter)
                            ->schema([
                                Textarea::make('text')
                                    ->label('Content')
                                    ->rows(5)
                                    ->maxLength(65535)
                                    ->required()
                                    ->columnSpanFull(),
                            ]),
                        Section::make('Metrics')
                            ->schema([
                                TextInput::make('count_likes')
                                    ->label('Likes')
                                    ->numeric()
                                    ->default(0)
                                    ->prefixIcon(Heroicon::Heart)
                                    ->required(),
                            ]),
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

                                Select::make('post_id')
                                    ->relationship('post', 'id')
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

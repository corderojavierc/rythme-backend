<?php

namespace App\Filament\Admin\Resources\Posts\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use App\Models\User;

class PostForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
            Select::make('user_id')
                ->relationship('user', 'username')
                ->searchable()
                ->getSearchResultsUsing(fn (string $search) =>
                    User::where('name', 'like', "%{$search}%")
                        ->orWhere('second_name', 'like', "%{$search}%")
                        ->orWhere('username', 'like', "%{$search}%")
                        ->limit(50)
                        ->get()
                        ->mapWithKeys(fn ($user) => [
                            $user->id => "{$user->name} {$user->second_name} (@{$user->username})"
                        ])
                )
                ->getOptionLabelFromRecordUsing(fn ($record) => "{$record->name} {$record->second_name} (@{$record->username})")
                ->preload()
                ->disabledOn('edit')
                ->required(),
                Select::make('music_id')
                    ->relationship('music', 'title')
                    ->disabledOn('edit')
                    ->required(),
                TextInput::make('text')
                    ->disabledOn('edit')
                    ->required(),
                    TextInput::make('rating')
                        ->numeric()
                        ->minValue(0)
                        ->maxValue(5)
                        ->step(0.1)
                        ->disabledOn('edit')
                        ->required(),
                TextInput::make('count_likes')
                    ->required()
                    ->numeric()
                    ->disabledOn('edit')
                    ->default(0),
                TextInput::make('count_repost')
                    ->required()
                    ->numeric()
                    ->disabledOn('edit')
                    ->default(0),
            ]);
    }
}

<?php

namespace App\Filament\Admin\Resources\Likes\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class LikeForm
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
                ->required(),
                Select::make('target_type')
                    ->options([
                        'post' => 'Post',
                        'comment' => 'Comment',
                    ])
                    ->required(),
                Select::make('target_id')
                    ->relationship('target', 'id')
                    ->required(),
            ]);
    }
}

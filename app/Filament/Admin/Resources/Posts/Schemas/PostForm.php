<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\Posts\Schemas;

use App\Models\User;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

final class PostForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('user_id')
                    ->relationship('user', 'username')
                    ->searchable()
                    ->getSearchResultsUsing(fn (string $search) => User::query()->where('name', 'like', sprintf('%%%s%%', $search))
                        ->orWhere('second_name', 'like', sprintf('%%%s%%', $search))
                        ->orWhere('username', 'like', sprintf('%%%s%%', $search))
                        ->limit(50)
                        ->get()
                        ->mapWithKeys(fn ($user): array => [
                            $user->id => sprintf('%s %s (@%s)', $user->name, $user->second_name, $user->username),
                        ])
                    )
                    ->getOptionLabelFromRecordUsing(fn ($record): string => sprintf('%s %s (@%s)', $record->name, $record->second_name, $record->username))
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
                    ->minValue(0)
                    ->default(0)
                    ->step(1)
                    ->disabledOn('edit'),
            ]);
    }
}

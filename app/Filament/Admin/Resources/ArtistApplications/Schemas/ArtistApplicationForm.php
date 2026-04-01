<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\ArtistApplications\Schemas;

use App\Models\User;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

final class ArtistApplicationForm
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
                    ->required(),
                Toggle::make('artist')
                    ->required(),
                TextInput::make('followers')
                    ->required()
                    ->numeric()
                    ->minValue(0)
                    ->default(0)
                    ->step(1)
                    ->disabledOn('edit'),
                TextInput::make('listeners')
                    ->numeric()
                    ->minValue(0)
                    ->default(0)
                    ->step(1)
                    ->disabledOn('edit'),
                TextInput::make('youtube'),
                TextInput::make('tiktok'),
                TextInput::make('instagram'),
                TextInput::make('spotify'),
                TextInput::make('twitch'),
                TextInput::make('description')
                    ->required(),
            ]);
    }
}

<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\Events\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

final class EventForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('user_id')
                    ->relationship('user', 'username')
                    ->searchable()
                    ->getSearchResultsUsing(fn (string $search) => User::where('name', 'like', sprintf('%%%s%%', $search))
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
                TextInput::make('title')
                    ->required(),
                TextInput::make('description')
                    ->required(),
                TextInput::make('location')
                    ->required(),
                DateTimePicker::make('date')
                    ->required(),
                FileUpload::make('image')
                    ->image()
                    ->dehydrated(fn ($state): bool => filled($state)),
                TextInput::make('capacity')
                    ->required(),
            ]);
    }
}

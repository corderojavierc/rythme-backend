<?php

namespace App\Filament\Admin\Resources\Events\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DateTimePicker;
use Filament\Schemas\Schema;

class EventForm
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
                ->dehydrated(fn ($state) => filled($state)),
                TextInput::make('capacity')
                    ->required(),
            ]);
    }
}

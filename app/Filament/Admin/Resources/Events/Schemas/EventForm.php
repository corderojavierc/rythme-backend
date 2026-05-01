<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\Events\Schemas;

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
                    ->relationship('user', 'name')
                    ->required(),
                TextInput::make('title')
                    ->required(),
                TextInput::make('description')
                    ->required(),
                TextInput::make('location')
                    ->required(),
                TextInput::make('date')
                    ->required(),
                FileUpload::make('image')
                    ->image(),
                TextInput::make('capacity')
                    ->required(),
            ]);
    }
}

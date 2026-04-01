<?php

namespace App\Filament\Admin\Resources\Music\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Schemas\Schema;

class MusicForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('title')
                    ->disabledOn('edit')
                    ->required(),
                TextInput::make('artist')
                    ->required(),
                FileUpload::make('cover_url')
                    ->image()
                    ->dehydrated(fn (string $state): bool => filled($state))
                    ->required(),
                DateTimePicker::make('release_date')
                    ->required(),
            ]);
    }
}

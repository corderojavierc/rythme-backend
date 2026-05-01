<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\Music\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

final class MusicForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('title')
                    ->required(),
                TextInput::make('artist')
                    ->required(),
                TextInput::make('cover_url')
                    ->url()
                    ->required(),
                TextInput::make('release_date')
                    ->required(),
            ]);
    }
}

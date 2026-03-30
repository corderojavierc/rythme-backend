<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\Music;

use App\Filament\Admin\Resources\Music\Pages\CreateMusic;
use App\Filament\Admin\Resources\Music\Pages\EditMusic;
use App\Filament\Admin\Resources\Music\Pages\ListMusic;
use App\Filament\Admin\Resources\Music\Schemas\MusicForm;
use App\Filament\Admin\Resources\Music\Tables\MusicTable;
use App\Models\Music;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Override;

final class MusicResource extends Resource
{
    #[Override]
    protected static ?string $model = Music::class;

    #[Override]
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    #[Override]
    protected static ?string $recordTitleAttribute = 'title';

    public static function form(Schema $schema): Schema
    {
        return MusicForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return MusicTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListMusic::route('/'),
            'create' => CreateMusic::route('/create'),
            'edit' => EditMusic::route('/{record}/edit'),
        ];
    }
}

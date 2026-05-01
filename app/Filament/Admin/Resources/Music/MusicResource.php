<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\Music;

use App\Filament\Admin\Resources\Music\Pages\CreateMusic;
use App\Filament\Admin\Resources\Music\Pages\EditMusic;
use App\Filament\Admin\Resources\Music\Pages\ListMusic;
use App\Filament\Admin\Resources\Music\Pages\ViewMusic;
use App\Filament\Admin\Resources\Music\Schemas\MusicForm;
use App\Filament\Admin\Resources\Music\Schemas\MusicInfolist;
use App\Filament\Admin\Resources\Music\Tables\MusicTable;
use App\Models\Music;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Override;
use UnitEnum;

final class MusicResource extends Resource
{
    #[Override]
    protected static ?string $model = Music::class;

    #[Override]
    protected static string|UnitEnum|null $navigationGroup = 'Music';

    #[Override]
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedMusicalNote;

    #[Override]
    protected static string|BackedEnum|null $activeNavigationIcon = Heroicon::MusicalNote;

    #[Override]
    protected static ?string $recordTitleAttribute = 'title';

    #[Override]
    protected static ?int $navigationSort = 3;

    public static function form(Schema $schema): Schema
    {
        return MusicForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return MusicInfolist::configure($schema);
    }

    public static function getGloballySearchableAttributes(): array
    {
        return [
            'title',
            'artist',
        ];
    }

    public static function getGlobalSearchResultTitle(Model $record): string
    {
        return $record->title ?? 'Unknown Title';
    }

    public static function getGlobalSearchResultDetails(Model $record): array
    {
        return [
            'Artist' => $record->artist ?? 'N/A',
        ];
    }

    public static function getNavigationBadge(): string
    {
        return (string) self::getModel()::query()->count();
    }

    public static function table(Table $table): Table
    {
        return MusicTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListMusic::route('/'),
            'create' => CreateMusic::route('/create'),
            'view' => ViewMusic::route('/{record}'),
            'edit' => EditMusic::route('/{record}/edit'),
        ];
    }
}

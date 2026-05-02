<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\MusicRatings;

use App\Filament\Admin\Resources\MusicRatings\Pages\CreateMusicRating;
use App\Filament\Admin\Resources\MusicRatings\Pages\EditMusicRating;
use App\Filament\Admin\Resources\MusicRatings\Pages\ListMusicRatings;
use App\Filament\Admin\Resources\MusicRatings\Pages\ViewMusicRating;
use App\Filament\Admin\Resources\MusicRatings\Schemas\MusicRatingForm;
use App\Filament\Admin\Resources\MusicRatings\Schemas\MusicRatingInfolist;
use App\Filament\Admin\Resources\MusicRatings\Tables\MusicRatingsTable;
use App\Models\MusicRating;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Override;
use UnitEnum;

final class MusicRatingResource extends Resource
{
    #[Override]
    protected static ?string $model = MusicRating::class;

    #[Override]
    protected static string|UnitEnum|null $navigationGroup = 'Music';

    #[Override]
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedStar;

    #[Override]
    protected static string|BackedEnum|null $activeNavigationIcon = Heroicon::Star;

    #[Override]
    protected static ?int $navigationSort = 2;

    public static function form(Schema $schema): Schema
    {
        return MusicRatingForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return MusicRatingInfolist::configure($schema);
    }

    public static function getGloballySearchableAttributes(): array
    {
        return [
            'music.title',
            'music.artist',
            'music_id',
        ];
    }

    public static function getGlobalSearchResultTitle(Model $record): string
    {
        /** @var MusicRating $record */
        return $record->music->title ?? 'Unknown Track';
    }

    public static function getGlobalSearchResultDetails(Model $record): array
    {
        /** @var MusicRating $record */
        return [
            'Artist' => $record->music->artist ?? 'N/A',
            'Rating' => (string) ($record->rating ?? '0.00'),
            'Total' => (string) ($record->count_ratings ?? '0'),
        ];
    }

    public static function getNavigationBadge(): string
    {
        return (string) self::getModel()::query()->count();
    }

    public static function table(Table $table): Table
    {
        return MusicRatingsTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListMusicRatings::route('/'),
            'create' => CreateMusicRating::route('/create'),
            'view' => ViewMusicRating::route('/{record}'),
            'edit' => EditMusicRating::route('/{record}/edit'),
        ];
    }
}

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
use Override;

final class MusicRatingResource extends Resource
{
    #[Override]
    protected static ?string $model = MusicRating::class;

    #[Override]
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    public static function form(Schema $schema): Schema
    {
        return MusicRatingForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return MusicRatingInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return MusicRatingsTable::configure($table);
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
            'index' => ListMusicRatings::route('/'),
            'create' => CreateMusicRating::route('/create'),
            'view' => ViewMusicRating::route('/{record}'),
            'edit' => EditMusicRating::route('/{record}/edit'),
        ];
    }
}

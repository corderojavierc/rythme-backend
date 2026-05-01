<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\ArtistApplications;

use App\Filament\Admin\Resources\ArtistApplications\Pages\CreateArtistApplication;
use App\Filament\Admin\Resources\ArtistApplications\Pages\EditArtistApplication;
use App\Filament\Admin\Resources\ArtistApplications\Pages\ListArtistApplications;
use App\Filament\Admin\Resources\ArtistApplications\Pages\ViewArtistApplication;
use App\Filament\Admin\Resources\ArtistApplications\Schemas\ArtistApplicationForm;
use App\Filament\Admin\Resources\ArtistApplications\Schemas\ArtistApplicationInfolist;
use App\Filament\Admin\Resources\ArtistApplications\Tables\ArtistApplicationsTable;
use App\Models\ArtistApplication;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Override;
use UnitEnum;

final class ArtistApplicationResource extends Resource
{
    #[Override]
    protected static ?string $model = ArtistApplication::class;

    #[Override]
    protected static string|UnitEnum|null $navigationGroup = 'Users';

    #[Override]
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedIdentification;

    #[Override]
    protected static string|BackedEnum|null $activeNavigationIcon = Heroicon::Identification;

    #[Override]
    protected static ?string $recordTitleAttribute = 'id';

    #[Override]
    protected static ?int $navigationSort = 1;

    public static function form(Schema $schema): Schema
    {
        return ArtistApplicationForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return ArtistApplicationInfolist::configure($schema);
    }

    public static function getGloballySearchableAttributes(): array
    {
        return [
            'id',
            'user.username',
            'user.email',
        ];
    }

    public static function getGlobalSearchResultTitle(Model $record): string
    {
        return 'Application by @'.($record->user->username ?? 'Unknown');
    }

    public static function getGlobalSearchResultDetails(Model $record): array
    {
        return [
            'Status' => $record->status->name ?? 'N/A',
            'Type' => $record->type->name ?? 'N/A',
        ];
    }

    public static function getNavigationBadge(): string
    {
        return (string) self::getModel()::query()->count();
    }

    public static function table(Table $table): Table
    {
        return ArtistApplicationsTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListArtistApplications::route('/'),
            'create' => CreateArtistApplication::route('/create'),
            'view' => ViewArtistApplication::route('/{record}'),
            'edit' => EditArtistApplication::route('/{record}/edit'),
        ];
    }
}

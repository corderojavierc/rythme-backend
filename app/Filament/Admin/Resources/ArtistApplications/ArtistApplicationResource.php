<?php

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
use UnitEnum;

class ArtistApplicationResource extends Resource
{
    protected static ?string $model = ArtistApplication::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static string|UnitEnum|null $navigationGroup = 'Users';

    public static function form(Schema $schema): Schema
    {
        return ArtistApplicationForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return ArtistApplicationInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ArtistApplicationsTable::configure($table);
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
            'index' => ListArtistApplications::route('/'),
            'create' => CreateArtistApplication::route('/create'),
            'view' => ViewArtistApplication::route('/{record}'),
            'edit' => EditArtistApplication::route('/{record}/edit'),
        ];
    }
}

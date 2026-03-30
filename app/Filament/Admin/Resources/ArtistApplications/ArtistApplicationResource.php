<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\ArtistApplications;

use App\Filament\Admin\Resources\ArtistApplications\Pages\CreateArtistApplication;
use App\Filament\Admin\Resources\ArtistApplications\Pages\EditArtistApplication;
use App\Filament\Admin\Resources\ArtistApplications\Pages\ListArtistApplications;
use App\Filament\Admin\Resources\ArtistApplications\Schemas\ArtistApplicationForm;
use App\Filament\Admin\Resources\ArtistApplications\Tables\ArtistApplicationsTable;
use App\Models\ArtistApplication;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Override;

final class ArtistApplicationResource extends Resource
{
    #[Override]
    protected static ?string $model = ArtistApplication::class;

    #[Override]
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    public static function form(Schema $schema): Schema
    {
        return ArtistApplicationForm::configure($schema);
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
            'edit' => EditArtistApplication::route('/{record}/edit'),
        ];
    }
}

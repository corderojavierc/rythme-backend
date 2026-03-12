<?php

namespace App\Filament\Admin\Resources\Likes;

use App\Filament\Admin\Resources\Likes\Pages\CreateLike;
use App\Filament\Admin\Resources\Likes\Pages\EditLike;
use App\Filament\Admin\Resources\Likes\Pages\ListLikes;
use App\Filament\Admin\Resources\Likes\Schemas\LikeForm;
use App\Filament\Admin\Resources\Likes\Tables\LikesTable;
use App\Models\Like;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class LikeResource extends Resource
{
    protected static ?string $model = Like::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'Like';

    public static function form(Schema $schema): Schema
    {
        return LikeForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return LikesTable::configure($table);
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
            'index' => ListLikes::route('/'),
            'create' => CreateLike::route('/create'),
            'edit' => EditLike::route('/{record}/edit'),
        ];
    }
}

<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\Likes;

use App\Filament\Admin\Resources\Likes\Pages\CreateLike;
use App\Filament\Admin\Resources\Likes\Pages\EditLike;
use App\Filament\Admin\Resources\Likes\Pages\ListLikes;
use App\Filament\Admin\Resources\Likes\Pages\ViewLike;
use App\Filament\Admin\Resources\Likes\Schemas\LikeForm;
use App\Filament\Admin\Resources\Likes\Schemas\LikeInfolist;
use App\Filament\Admin\Resources\Likes\Tables\LikesTable;
use App\Models\Like;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Override;
use UnitEnum;

final class LikeResource extends Resource
{
    #[Override]
    protected static ?string $model = Like::class;

    #[Override]
    protected static string|UnitEnum|null $navigationGroup = 'Content';

    #[Override]
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedHeart;

    #[Override]
    protected static string|BackedEnum|null $activeNavigationIcon = Heroicon::Heart;

    #[Override]
    protected static ?string $recordTitleAttribute = 'id';

    #[Override]
    protected static ?int $navigationSort = 3;

    public static function form(Schema $schema): Schema
    {
        return LikeForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return LikeInfolist::configure($schema);
    }

    public static function getGloballySearchableAttributes(): array
    {
        return [
            'user.username',
            'likeable_type',
        ];
    }

    public static function getGlobalSearchResultTitle(Model $record): string
    {
        return 'Like by @'.($record->user->username ?? 'Unknown');
    }

    public static function getGlobalSearchResultDetails(Model $record): array
    {
        /** @var Like $record */
        return [
            'Target' => class_basename($record->likeable_type ?? ''),
            'Target ID' => (string) $record->likeable_id,
        ];
    }

    public static function getNavigationBadge(): string
    {
        return (string) self::getModel()::query()->count();
    }

    public static function table(Table $table): Table
    {
        return LikesTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListLikes::route('/'),
            'create' => CreateLike::route('/create'),
            'view' => ViewLike::route('/{record}'),
            'edit' => EditLike::route('/{record}/edit'),
        ];
    }
}

<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\Posts;

use App\Filament\Admin\Resources\Posts\Pages\CreatePost;
use App\Filament\Admin\Resources\Posts\Pages\EditPost;
use App\Filament\Admin\Resources\Posts\Pages\ListPosts;
use App\Filament\Admin\Resources\Posts\Pages\ViewPost;
use App\Filament\Admin\Resources\Posts\Schemas\PostForm;
use App\Filament\Admin\Resources\Posts\Schemas\PostInfolist;
use App\Filament\Admin\Resources\Posts\Tables\PostsTable;
use App\Models\Post;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Override;
use UnitEnum;

final class PostResource extends Resource
{
    #[Override]
    protected static ?string $model = Post::class;

    #[Override]
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedDocumentText;

    #[Override]
    protected static string|BackedEnum|null $activeNavigationIcon = Heroicon::DocumentText;

    #[Override]
    protected static string|UnitEnum|null $navigationGroup = 'Content';

    #[Override]
    protected static ?int $navigationSort = 1;

    public static function form(Schema $schema): Schema
    {
        return PostForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return PostInfolist::configure($schema);
    }

    public static function getGloballySearchableAttributes(): array
    {
        return [
            'id',
            'text',
        ];
    }

    public static function getGlobalSearchResultTitle(Model $record): string
    {
        /** @var Post $record */
        return Str::limit($record->text ?? 'Post sin texto', 30);
    }

    public static function getGlobalSearchResultDetails(Model $record): array
    {
        /** @var Post $record */
        return [
            'Author' => $record->user->username ?? 'N/A',
            'Rating' => (string) ($record->rating ?? '0'),
        ];
    }

    public static function getNavigationBadge(): string
    {
        return (string) self::getModel()::query()->count();
    }

    public static function table(Table $table): Table
    {
        return PostsTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListPosts::route('/'),
            'create' => CreatePost::route('/create'),
            'view' => ViewPost::route('/{record}'),
            'edit' => EditPost::route('/{record}/edit'),
        ];
    }
}

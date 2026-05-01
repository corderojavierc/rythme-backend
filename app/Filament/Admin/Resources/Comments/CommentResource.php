<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\Comments;

use App\Filament\Admin\Resources\Comments\Pages\CreateComment;
use App\Filament\Admin\Resources\Comments\Pages\EditComment;
use App\Filament\Admin\Resources\Comments\Pages\ListComments;
use App\Filament\Admin\Resources\Comments\Pages\ViewComment;
use App\Filament\Admin\Resources\Comments\Schemas\CommentForm;
use App\Filament\Admin\Resources\Comments\Schemas\CommentInfolist;
use App\Filament\Admin\Resources\Comments\Tables\CommentsTable;
use App\Models\Comment;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Override;
use UnitEnum;

final class CommentResource extends Resource
{
    #[Override]
    protected static ?string $model = Comment::class;

    #[Override]
    protected static string|UnitEnum|null $navigationGroup = 'Content';

    #[Override]
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedChatBubbleLeftEllipsis;

    #[Override]
    protected static string|BackedEnum|null $activeNavigationIcon = Heroicon::ChatBubbleLeftEllipsis;

    #[Override]
    protected static ?string $recordTitleAttribute = 'id';

    #[Override]
    protected static ?int $navigationSort = 2;

    public static function form(Schema $schema): Schema
    {
        return CommentForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return CommentInfolist::configure($schema);
    }

    public static function getGloballySearchableAttributes(): array
    {
        return [
            'id',
            'text',
            'user.username',
        ];
    }

    public static function getGlobalSearchResultTitle(Model $record): string
    {
        return Str::limit($record->text ?? 'Empty comment', 30);
    }

    public static function getGlobalSearchResultDetails(Model $record): array
    {
        return [
            'Author' => $record->user->username ?? 'Unknown',
            'Likes' => (string) ($record->count_likes ?? '0'),
        ];
    }

    public static function getNavigationBadge(): string
    {
        return (string) self::getModel()::query()->count();
    }

    public static function table(Table $table): Table
    {
        return CommentsTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListComments::route('/'),
            'create' => CreateComment::route('/create'),
            'view' => ViewComment::route('/{record}'),
            'edit' => EditComment::route('/{record}/edit'),
        ];
    }
}

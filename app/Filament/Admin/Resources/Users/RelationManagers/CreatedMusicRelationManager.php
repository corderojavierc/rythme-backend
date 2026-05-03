<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\Users\RelationManagers;

use App\Enums\UserTypeEnum;
use App\Filament\Admin\Resources\Music\MusicResource;
use App\Models\User;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Override;

final class CreatedMusicRelationManager extends RelationManager
{
    #[Override]
    protected static string $relationship = 'createdMusic';

    #[Override]
    protected static ?string $relatedResource = MusicResource::class;

    public static function canViewForRecord(Model $ownerRecord, string $pageClass): bool
    {
        /** @var User $ownerRecord */
        return $ownerRecord->type === UserTypeEnum::ARTIST;
    }

    public function form(Schema $schema): Schema
    {
        return MusicResource::form($schema);
    }

    public function infolist(Schema $schema): Schema
    {
        return MusicResource::infolist($schema);
    }

    public function table(Table $table): Table
    {
        return MusicResource::table($table);
    }
}

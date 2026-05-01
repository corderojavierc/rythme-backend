<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\Likes\Tables;

use App\Models\Comment;
use App\Models\Post;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

final class LikesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->label('ID')
                    ->sortable()
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('user.username')
                    ->label('User')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('likeable_type')
                    ->label('Type')
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => class_basename($state))
                    ->color(fn (string $state): string => match (class_basename($state)) {
                        'Post' => 'info',
                        'Comment' => 'warning',
                        default => 'gray',
                    }),

                TextColumn::make('likeable_id')
                    ->label('Target ID')
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('created_at')
                    ->label('Liked At')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('updated_at')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('likeable_type')
                    ->label('Target Type')
                    ->options([
                        Post::class => 'Post',
                        Comment::class => 'Comment',
                    ]),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc')
            ->striped();
    }
}

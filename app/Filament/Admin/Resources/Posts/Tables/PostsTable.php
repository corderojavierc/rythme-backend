<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\Posts\Tables;

use App\Models\Post;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

final class PostsTable
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
                    ->label('Author')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('music.title')
                    ->label('Song')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('text')
                    ->label('Content')
                    ->limit(40)
                    ->tooltip(fn (Post $record): string => $record->text)
                    ->searchable(),

                TextColumn::make('rating')
                    ->numeric()
                    ->sortable()
                    ->badge()
                    ->color(fn (mixed $state): string => match (true) {
                        $state >= 4 => 'success',
                        $state >= 2 => 'warning',
                        default => 'danger',
                    }),

                TextColumn::make('count_likes')
                    ->label('Likes')
                    ->numeric()
                    ->sortable()
                    ->icon('heroicon-o-heart'),

                TextColumn::make('count_comments')
                    ->label('Comments')
                    ->numeric()
                    ->sortable()
                    ->icon('heroicon-o-chat-bubble-left'),

                TextColumn::make('created_at')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('updated_at')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                ViewAction::make(),
                DeleteAction::make(),
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

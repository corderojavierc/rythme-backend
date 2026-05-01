<?php

namespace App\Filament\Admin\Resources\Posts\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class PostsTable
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
                    ->tooltip(fn ($record) => $record->text)
                    ->searchable(),

                TextColumn::make('rating')
                    ->numeric()
                    ->sortable()
                    ->badge()
                    ->color(fn ($state) => match(true) {
                        $state >= 4 => 'success',
                        $state >= 2 => 'warning',
                        default     => 'danger',
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

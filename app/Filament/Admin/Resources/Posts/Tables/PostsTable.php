<?php

namespace App\Filament\Admin\Resources\Posts\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\DeleteAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Actions\ViewAction;
use Filament\Tables\Table;

class PostsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->label('ID')
                    ->searchable(),
                TextColumn::make('user.username')
                    ->searchable(),
                TextColumn::make('music.title')
                    ->searchable(),
                TextColumn::make('text')
                    ->searchable(),
                TextColumn::make('rating')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('count_likes')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('count_repost')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->recordActions([
               DeleteAction::make(),
            ])
            ->actions([
                ViewAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}

<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\MusicRatings\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

final class MusicRatingsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('music.title')
                    ->label('Song')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('rating')
                    ->numeric()
                    ->sortable()
                    ->badge()
                    ->color(fn (mixed $state): string => match (true) {
                        $state >= 4 => 'success',
                        $state >= 2 => 'warning',
                        default => 'danger',
                    }),

                TextColumn::make('count_ratings')
                    ->label('Total Ratings')
                    ->numeric()
                    ->sortable()
                    ->icon('heroicon-o-star'),

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
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('rating', 'desc')
            ->striped();
    }
}

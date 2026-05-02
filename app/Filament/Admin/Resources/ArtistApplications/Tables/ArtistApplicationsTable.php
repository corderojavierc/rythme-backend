<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\ArtistApplications\Tables;

use App\Enums\ArtistApplicationStatusEnum;
use App\Enums\UserTypeEnum;
use App\Models\ArtistApplication;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

final class ArtistApplicationsTable
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

                TextColumn::make('user.name')
                    ->label('Applicant')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('type')
                    ->badge()
                    ->sortable()
                    ->color(fn (mixed $state): string => match ($state) {
                        'artist' => 'info',
                        'band' => 'warning',
                        default => 'gray',
                    }),

                TextColumn::make('status')
                    ->badge()
                    ->sortable()
                    ->color(fn (mixed $state): string => match ($state) {
                        'approved' => 'success',
                        'pending' => 'warning',
                        'rejected' => 'danger',
                        default => 'gray',
                    }),

                TextColumn::make('followers')
                    ->numeric()
                    ->sortable()
                    ->icon('heroicon-o-users'),

                TextColumn::make('listeners')
                    ->numeric()
                    ->sortable()
                    ->icon('heroicon-o-musical-note'),

                TextColumn::make('youtube')
                    ->icon('heroicon-o-video-camera')
                    ->url(fn (ArtistApplication $record): ?string => $record->youtube)
                    ->openUrlInNewTab()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('tiktok')
                    ->icon('heroicon-o-device-phone-mobile')
                    ->url(fn (ArtistApplication $record): ?string => $record->tiktok)
                    ->openUrlInNewTab()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('instagram')
                    ->icon('heroicon-o-camera')
                    ->url(fn (ArtistApplication $record): ?string => $record->instagram)
                    ->openUrlInNewTab()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('spotify')
                    ->icon('heroicon-o-musical-note')
                    ->url(fn (ArtistApplication $record): ?string => $record->spotify)
                    ->openUrlInNewTab()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('twitch')
                    ->icon('heroicon-o-tv')
                    ->url(fn (ArtistApplication $record): ?string => $record->twitch)
                    ->openUrlInNewTab()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('created_at')
                    ->label('Applied At')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('updated_at')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options(ArtistApplicationStatusEnum::class)
                    ->default(ArtistApplicationStatusEnum::SENT),
                SelectFilter::make('type')
                    ->options(UserTypeEnum::class),
            ])
            ->recordActions([
                ViewAction::make(),
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

<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\ArtistApplications\Tables;

use App\Enums\ArtistApplicationStatusEnum;
use App\Enums\UserTypeEnum;
use App\Models\ArtistApplication;
use App\Services\SpotifyService;
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
                    ->sortable(),

                TextColumn::make('status')
                    ->badge()
                    ->sortable(),

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
                    ->label('Spotify Artist')
                    ->icon('heroicon-o-musical-note')
                    ->formatStateUsing(function (string $state): ?string {
                        if ($state === '' || $state === '0') {
                            return null;
                        }

                        $cleanId = $state;

                        if (preg_match('/artist\/([a-zA-Z0-9]+)/', $state, $matches)) {
                            $cleanId = $matches[1];
                        }

                        return SpotifyService::getArtistName($cleanId) ?? $state;
                    })
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

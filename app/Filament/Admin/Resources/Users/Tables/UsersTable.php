<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\Users\Tables;

use App\Models\User;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

final class UsersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('profile_image')
                    ->circular()
                    ->defaultImageUrl(fn (User $record): string => 'https://api.dicebear.com/9.x/thumbs/svg?seed='.urlencode((string) $record->username)),

                TextColumn::make('id')
                    ->label('ID')
                    ->sortable()
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('username')
                    ->searchable()
                    ->sortable()
                    ->copyable(),

                TextColumn::make('type')
                    ->badge()
                    ->sortable()
                    ->color(fn (mixed $state): string => match ($state) {
                        'admin' => 'danger',
                        'mod' => 'warning',
                        default => 'gray',
                    }),

                TextColumn::make('email_verified_at')
                    ->label('Verified')
                    ->dateTime('d/m/Y')
                    ->sortable()
                    ->badge()
                    ->color(fn (mixed $state): string => $state ? 'success' : 'danger')
                    ->formatStateUsing(fn (mixed $state): string => $state ? 'Yes' : 'No'),

                TextColumn::make('followers')
                    ->numeric()
                    ->sortable()
                    ->icon('heroicon-o-users'),

                TextColumn::make('following')
                    ->numeric()
                    ->sortable()
                    ->icon('heroicon-o-user-plus'),

                TextColumn::make('posts')
                    ->numeric()
                    ->sortable()
                    ->icon('heroicon-o-document-text'),

                TextColumn::make('created_at')
                    ->label('Joined')
                    ->dateTime('d/m/Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('updated_at')
                    ->dateTime('d/m/Y')
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

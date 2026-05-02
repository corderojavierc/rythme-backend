<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\ArtistApplications\Pages;

use App\Enums\ArtistApplicationStatusEnum;
use App\Filament\Admin\Resources\ArtistApplications\ArtistApplicationResource;
use App\Models\ArtistApplication;
use Filament\Actions\Action;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;
use Override;

final class ViewArtistApplication extends ViewRecord
{
    #[Override]
    protected static string $resource = ArtistApplicationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('accept')
                ->label('Accept')
                ->hidden(function (): bool {
                    /** @var ArtistApplication $record */
                    $record = $this->getRecord();

                    return $record->status !== ArtistApplicationStatusEnum::SENT;
                })
                ->requiresConfirmation()
                ->form([
                    Textarea::make('admin_notes')
                        ->label('Reason for acceptance')
                        ->required(),
                ])
                ->action(function (array $data): void {
                    /** @var ArtistApplication $record */
                    $record = $this->getRecord();
                    $adminNotes = $data['admin_notes'];
                    $record->acceptApplication($record->id, $adminNotes);

                    Notification::make()
                        ->title('Application accepted')
                        ->success()
                        ->send();

                    $this->redirect(ArtistApplicationResource::getUrl('index'));
                }),

            Action::make('decline')
                ->label('Decline')
                ->color('secondary')
                ->requiresConfirmation()
                ->form([
                    Textarea::make('admin_notes')
                        ->label('Reason for decline')
                        ->required(),
                ])
                ->hidden(function (): bool {
                    /** @var ArtistApplication $record */
                    $record = $this->getRecord();

                    return $record->status !== ArtistApplicationStatusEnum::SENT;
                })
                ->action(function (array $data): void {
                    /** @var ArtistApplication $record */
                    $record = $this->getRecord();
                    $adminNotes = $data['admin_notes'];
                    $record->declineApplication($record->id, $adminNotes);

                    Notification::make()
                        ->title('Application declined')
                        ->success()
                        ->send();

                    $this->redirect(ArtistApplicationResource::getUrl('index'));
                }),
        ];
    }
}

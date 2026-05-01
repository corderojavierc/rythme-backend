<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\Events;

use App\Filament\Admin\Resources\Events\Pages\CreateEvent;
use App\Filament\Admin\Resources\Events\Pages\EditEvent;
use App\Filament\Admin\Resources\Events\Pages\ListEvents;
use App\Filament\Admin\Resources\Events\Pages\ViewEvent;
use App\Filament\Admin\Resources\Events\Schemas\EventForm;
use App\Filament\Admin\Resources\Events\Schemas\EventInfolist;
use App\Filament\Admin\Resources\Events\Tables\EventsTable;
use App\Models\Event;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Override;
use UnitEnum;

final class EventResource extends Resource
{
    #[Override]
    protected static ?string $model = Event::class;

    #[Override]
    protected static string|UnitEnum|null $navigationGroup = 'Events';

    #[Override]
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCalendar;

    #[Override]
    protected static string|BackedEnum|null $activeNavigationIcon = Heroicon::Calendar;

    #[Override]
    protected static ?string $recordTitleAttribute = 'title';

    #[Override]
    protected static ?int $navigationSort = 4;

    public static function form(Schema $schema): Schema
    {
        return EventForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return EventInfolist::configure($schema);
    }

    public static function getGloballySearchableAttributes(): array
    {
        return [
            'title',
            'location',
        ];
    }

    public static function getGlobalSearchResultTitle(Model $record): string
    {
        return $record->title ?? 'Unknown Event';
    }

    public static function getGlobalSearchResultDetails(Model $record): array
    {
        return [
            'Location' => $record->location ?? 'N/A',
            'Date' => $record->date ?? 'N/A',
        ];
    }

    public static function getNavigationBadge(): string
    {
        return (string) self::getModel()::query()->count();
    }

    public static function table(Table $table): Table
    {
        return EventsTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListEvents::route('/'),
            'create' => CreateEvent::route('/create'),
            'view' => ViewEvent::route('/{record}'),
            'edit' => EditEvent::route('/{record}/edit'),
        ];
    }
}

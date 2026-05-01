<?php

declare(strict_types=1);

namespace App\Providers;

use App\Models\Comment;
use App\Models\Post;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Field;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Infolists\Components\Entry;
use Filament\Livewire\Notifications;
use Filament\Resources\Pages\CreateRecord;
use Filament\Schemas\Components\FusedGroup;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Components\Wizard;
use Filament\Schemas\Components\Wizard\Step;
use Filament\Support\Components\Component;
use Filament\Support\Enums\Alignment;
use Filament\Support\Enums\IconSize;
use Filament\Support\Enums\Width;
use Filament\Tables\Columns\Column;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\BaseFilter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\ServiceProvider;

final class AppServiceProvider extends ServiceProvider
{
    public function register(): void {}

    public function boot(): void
    {
        Relation::morphMap([
            'post'    => Post::class,
            'comment' => Comment::class,
        ]);

        $this->translatableComponents();
        $this->configureNotifications();
        $this->configureActions();
        $this->configureTable();
        $this->configureFormComponents();
        $this->configureInfolistComponents();
        $this->configureLayoutComponents();
    }

    private function translatableComponents(): void
    {
        $classes = [
            Field::class,
            BaseFilter::class,
            Tabs::class,
            Action::class,
            Section::class,
            Wizard::class,
            Step::class,
            Column::class,
            Entry::class,
            FusedGroup::class,
            Tab::class,
        ];

        foreach ($classes as $component) {
            $component::configureUsing(function (Component $component): void {
                $component->translateLabel();
            });
        }
    }

    private function configureNotifications(): void
    {
        Notifications::alignment(Alignment::Center);
        Notifications::verticalAlignment(\Filament\Support\Enums\VerticalAlignment::End);
    }

    private function configureActions(): void
    {
        Action::configureUsing(function (Action $action): void {
            $action
                ->modalWidth(Width::TwoExtraLarge)
                ->modalFooterActionsAlignment(Alignment::End);
        });

        DeleteAction::configureUsing(function (DeleteAction $deleteAction): void {
            $deleteAction
                ->requiresConfirmation()
                ->modalFooterActionsAlignment(Alignment::Center);
        });

        ViewAction::configureUsing(function (ViewAction $viewAction): void {
            $viewAction
                ->icon(null)
                ->modalWidth(Width::ScreenExtraLarge);
        });

        EditAction::configureUsing(function (EditAction $editAction): void {
            $editAction->modalWidth(Width::ScreenExtraLarge);
        });

        CreateRecord::formActionsAlignment(Alignment::Right);
        CreateRecord::disableCreateAnother();
    }

    private function configureTable(): void
    {
        Table::configureUsing(function (Table $table): void {
            $table
                ->striped()
                ->deferLoading()
                ->defaultSort('id', 'desc')
                ->defaultNumberLocale('es')
                ->defaultCurrency('eur')
                ->defaultDateDisplayFormat('d M Y')
                ->defaultDateTimeDisplayFormat('M j, Y H:i')
                ->modifyUngroupedRecordActionsUsing(
                    fn (Action $action): Action => $action
                        ->iconSize(IconSize::Small)
                        ->iconButton()
                );
        });

        TextColumn::configureUsing(function (TextColumn $column): void {
            $column->placeholder('-');
        });

        TernaryFilter::configureUsing(function (TernaryFilter $ternaryFilter): void {
            $ternaryFilter->native(false);
        });

        SelectFilter::configureUsing(function (SelectFilter $selectFilter): void {
            $selectFilter->native(false);
        });

        ImageColumn::configureUsing(function (ImageColumn $imageColumn): void {
            $imageColumn->visibility('public');
        });
    }

    private function configureInfolistComponents(): void
    {
        Entry::configureUsing(function (Entry $entry): void {
            $entry->placeholder('-');
        });
    }

    private function configureFormComponents(): void
    {
        Select::configureUsing(function (Select $select): void {
            $select->selectablePlaceholder(false)->native(false);
        });

        DateTimePicker::configureUsing(function (DateTimePicker $dateTimePicker): void {
            $dateTimePicker
                ->native(false)
                ->timezone(config('app.timezone'));
        });

        Toggle::configureUsing(function (Toggle $toggle): void {
            $toggle->default(false);
        });
    }

    private function configureLayoutComponents(): void
    {
        Section::configureUsing(function (Section $section): void {
            $section->columnSpanFull();
        });

        Grid::configureUsing(function (Grid $grid): void {
            $grid->columnSpanFull();
        });
    }
}

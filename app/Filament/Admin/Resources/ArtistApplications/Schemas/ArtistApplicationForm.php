<?php
namespace App\Filament\Admin\Resources\ArtistApplications\Schemas;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;
use App\Models\User;
class ArtistApplicationForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('user_id')
                    ->relationship('user', 'username')
                    ->searchable()
                    ->getSearchResultsUsing(fn (string $search) =>
                        User::where('name', 'like', "%{$search}%")
                            ->orWhere('second_name', 'like', "%{$search}%")
                            ->orWhere('username', 'like', "%{$search}%")
                            ->limit(50)
                            ->get()
                            ->mapWithKeys(fn ($user) => [
                                $user->id => "{$user->name} {$user->second_name} (@{$user->username})"
                            ])
                    )
                    ->getOptionLabelFromRecordUsing(fn ($record) => "{$record->name} {$record->second_name} (@{$record->username})")
                    ->preload()
                    ->required(),
                Toggle::make('artist')
                    ->required(),
                TextInput::make('followers')
                    ->required()
                    ->numeric(),
                TextInput::make('listeners')
                    ->numeric(),
                TextInput::make('youtube'),
                TextInput::make('tiktok'),
                TextInput::make('instagram'),
                TextInput::make('spotify'),
                TextInput::make('twitch'),
                TextInput::make('description')
                    ->required(),
            ]);
    }
}

<?php

declare(strict_types=1);

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;
use Filament\Support\Icons\Heroicon;

enum UserTypeEnum: string implements HasColor, HasIcon, HasLabel
{
    case ARTIST = 'artist';
    case CREATOR = 'creator';
    case ADMIN = 'admin';
    case USER = 'user';

    public function getLabel(): string
    {
        return match ($this) {
            self::ARTIST => 'Artist',
            self::CREATOR => 'Creator',
            self::ADMIN => 'Admin',
            self::USER => 'User',
        };
    }

    public function getIcon(): Heroicon
    {
        return match ($this) {
            self::ARTIST => Heroicon::MusicalNote,
            self::CREATOR => Heroicon::PaintBrush,
            self::ADMIN => Heroicon::Cog,
            self::USER => Heroicon::User,
        };
    }

    public function getColor(): string
    {
        return match ($this) {
            self::ARTIST => 'blue',
            self::CREATOR => 'green',
            self::ADMIN => 'purple',
            self::USER => 'gray',
        };
    }
}

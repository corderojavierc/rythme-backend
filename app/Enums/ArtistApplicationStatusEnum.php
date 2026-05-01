<?php

declare(strict_types=1);

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;
use Filament\Support\Icons\Heroicon;

enum ArtistApplicationStatusEnum: string implements HasColor, HasIcon, HasLabel
{
    case SENT = 'sent';
    case ACCEPTED = 'accepted';
    case DECLINED = 'declined';

    public function getLabel(): string
    {
        return match ($this) {
            self::SENT => 'Sent',
            self::ACCEPTED => 'Accepted',
            self::DECLINED => 'Declined',
        };
    }

    public function getIcon(): Heroicon
    {
        return match ($this) {
            self::SENT => Heroicon::InboxArrowDown,
            self::ACCEPTED => Heroicon::Check,
            self::DECLINED => Heroicon::XMark,
        };
    }

    public function getColor(): string
    {
        return match ($this) {
            self::SENT => 'blue',
            self::ACCEPTED => 'green',
            self::DECLINED => 'red',
        };
    }
}

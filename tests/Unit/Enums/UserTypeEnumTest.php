<?php

declare(strict_types=1);

use App\Enums\UserTypeEnum;
use Filament\Support\Icons\Heroicon;

it('has correct string values', function (): void {
    expect(UserTypeEnum::ARTIST->value)->toBe('artist');
    expect(UserTypeEnum::CREATOR->value)->toBe('creator');
    expect(UserTypeEnum::ADMIN->value)->toBe('admin');
    expect(UserTypeEnum::USER->value)->toBe('user');
});

it('can be created from a valid string value', function (): void {
    expect(UserTypeEnum::from('artist'))->toBe(UserTypeEnum::ARTIST);
    expect(UserTypeEnum::from('creator'))->toBe(UserTypeEnum::CREATOR);
    expect(UserTypeEnum::from('admin'))->toBe(UserTypeEnum::ADMIN);
    expect(UserTypeEnum::from('user'))->toBe(UserTypeEnum::USER);
});

it('returns null for invalid string via tryFrom', function (): void {
    expect(UserTypeEnum::tryFrom('invalid'))->toBeNull();
    expect(UserTypeEnum::tryFrom(''))->toBeNull();
});

it('returns correct labels', function (): void {
    expect(UserTypeEnum::ARTIST->getLabel())->toBe('Artist');
    expect(UserTypeEnum::CREATOR->getLabel())->toBe('Creator');
    expect(UserTypeEnum::ADMIN->getLabel())->toBe('Admin');
    expect(UserTypeEnum::USER->getLabel())->toBe('User');
});

it('returns correct icons', function (): void {
    expect(UserTypeEnum::ARTIST->getIcon())->toBe(Heroicon::MusicalNote);
    expect(UserTypeEnum::CREATOR->getIcon())->toBe(Heroicon::PaintBrush);
    expect(UserTypeEnum::ADMIN->getIcon())->toBe(Heroicon::Cog);
    expect(UserTypeEnum::USER->getIcon())->toBe(Heroicon::User);
});

it('returns correct colors', function (): void {
    expect(UserTypeEnum::ARTIST->getColor())->toBe('artist-color');
    expect(UserTypeEnum::CREATOR->getColor())->toBe('creator-color');
    expect(UserTypeEnum::ADMIN->getColor())->toBe('admin-color');
    expect(UserTypeEnum::USER->getColor())->toBe('gray');
});

it('has exactly four cases', function (): void {
    expect(UserTypeEnum::cases())->toHaveCount(4);
});

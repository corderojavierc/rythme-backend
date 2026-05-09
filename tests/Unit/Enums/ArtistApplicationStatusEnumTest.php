<?php

declare(strict_types=1);

use App\Enums\ArtistApplicationStatusEnum;
use Filament\Support\Icons\Heroicon;

it('has correct string values', function (): void {
    expect(ArtistApplicationStatusEnum::SENT->value)->toBe('sent');
    expect(ArtistApplicationStatusEnum::ACCEPTED->value)->toBe('accepted');
    expect(ArtistApplicationStatusEnum::DECLINED->value)->toBe('declined');
});

it('can be created from a valid string value', function (): void {
    expect(ArtistApplicationStatusEnum::from('sent'))->toBe(ArtistApplicationStatusEnum::SENT);
    expect(ArtistApplicationStatusEnum::from('accepted'))->toBe(ArtistApplicationStatusEnum::ACCEPTED);
    expect(ArtistApplicationStatusEnum::from('declined'))->toBe(ArtistApplicationStatusEnum::DECLINED);
});

it('returns null for invalid string via tryFrom', function (): void {
    expect(ArtistApplicationStatusEnum::tryFrom('pending'))->toBeNull();
    expect(ArtistApplicationStatusEnum::tryFrom(''))->toBeNull();
});

it('returns correct labels', function (): void {
    expect(ArtistApplicationStatusEnum::SENT->getLabel())->toBe('Sent');
    expect(ArtistApplicationStatusEnum::ACCEPTED->getLabel())->toBe('Accepted');
    expect(ArtistApplicationStatusEnum::DECLINED->getLabel())->toBe('Declined');
});

it('returns correct icons', function (): void {
    expect(ArtistApplicationStatusEnum::SENT->getIcon())->toBe(Heroicon::InboxArrowDown);
    expect(ArtistApplicationStatusEnum::ACCEPTED->getIcon())->toBe(Heroicon::Check);
    expect(ArtistApplicationStatusEnum::DECLINED->getIcon())->toBe(Heroicon::XMark);
});

it('returns correct colors', function (): void {
    expect(ArtistApplicationStatusEnum::SENT->getColor())->toBe('primary');
    expect(ArtistApplicationStatusEnum::ACCEPTED->getColor())->toBe('success');
    expect(ArtistApplicationStatusEnum::DECLINED->getColor())->toBe('danger');
});

it('has exactly three cases', function (): void {
    expect(ArtistApplicationStatusEnum::cases())->toHaveCount(3);
});

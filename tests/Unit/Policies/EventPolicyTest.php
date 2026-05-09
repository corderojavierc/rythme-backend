<?php

declare(strict_types=1);

use App\Enums\UserTypeEnum;
use App\Models\Event;
use App\Models\User;
use App\Policies\EventPolicy;

it('allows admin to view any event', function (): void {
    $admin = User::factory()->create(['type' => UserTypeEnum::ADMIN]);
    $policy = new EventPolicy();

    expect($policy->viewAny($admin))->toBeTrue();
});

it('denies non-admin to view any event', function (): void {
    $user = User::factory()->create(['type' => UserTypeEnum::USER]);
    $policy = new EventPolicy();

    expect($policy->viewAny($user))->toBeFalse();
});

it('allows admin to update an event', function (): void {
    $admin = User::factory()->create(['type' => UserTypeEnum::ADMIN]);
    $event = Event::factory()->create();
    $policy = new EventPolicy();

    expect($policy->update($admin))->toBeTrue();
});

it('denies non-admin to update an event', function (): void {
    $user = User::factory()->create(['type' => UserTypeEnum::USER]);
    $event = Event::factory()->create();
    $policy = new EventPolicy();

    expect($policy->update($user))->toBeFalse();
});

it('denies anyone to delete an event via policy', function (): void {
    $admin = User::factory()->create(['type' => UserTypeEnum::ADMIN]);
    $event = Event::factory()->create();
    $policy = new EventPolicy();

    expect($policy->delete())->toBeFalse();
});

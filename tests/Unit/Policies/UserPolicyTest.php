<?php

declare(strict_types=1);

use App\Enums\UserTypeEnum;
use App\Models\User;
use App\Policies\UserPolicy;

it('allows admin to view any user', function (): void {
    $admin = User::factory()->create(['type' => UserTypeEnum::ADMIN]);
    $policy = new UserPolicy();

    expect($policy->viewAny($admin))->toBeTrue();
});

it('denies non-admin to view any user', function (): void {
    $user = User::factory()->create(['type' => UserTypeEnum::USER]);
    $policy = new UserPolicy();

    expect($policy->viewAny($user))->toBeFalse();
});

it('allows admin to view a user', function (): void {
    $admin = User::factory()->create(['type' => UserTypeEnum::ADMIN]);
    $target = User::factory()->create();
    $policy = new UserPolicy();

    expect($policy->view($admin))->toBeTrue();
});

it('denies non-admin to view a user', function (): void {
    $user = User::factory()->create(['type' => UserTypeEnum::USER]);
    $target = User::factory()->create();
    $policy = new UserPolicy();

    expect($policy->view($user))->toBeFalse();
});

it('allows admin to create user', function (): void {
    $admin = User::factory()->create(['type' => UserTypeEnum::ADMIN]);
    $policy = new UserPolicy();

    expect($policy->create($admin))->toBeTrue();
});

it('denies non-admin to create user', function (): void {
    $user = User::factory()->create(['type' => UserTypeEnum::USER]);
    $policy = new UserPolicy();

    expect($policy->create($user))->toBeFalse();
});

it('allows admin to update user', function (): void {
    $admin = User::factory()->create(['type' => UserTypeEnum::ADMIN]);
    $target = User::factory()->create();
    $policy = new UserPolicy();

    expect($policy->update($admin))->toBeTrue();
});

it('denies non-admin to update user', function (): void {
    $user = User::factory()->create(['type' => UserTypeEnum::USER]);
    $target = User::factory()->create();
    $policy = new UserPolicy();

    expect($policy->update($user))->toBeFalse();
});

it('denies anyone to delete, restore, or forceDelete user', function (): void {
    $admin = User::factory()->create(['type' => UserTypeEnum::ADMIN]);
    $target = User::factory()->create();
    $policy = new UserPolicy();

    expect($policy->delete())->toBeFalse()
        ->and($policy->restore())->toBeFalse()
        ->and($policy->forceDelete())->toBeFalse();
});

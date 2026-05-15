<?php

declare(strict_types=1);

use App\Enums\UserTypeEnum;
use App\Models\Music;
use App\Models\User;
use App\Policies\MusicPolicy;

it('allows admin to view any music', function (): void {
    $admin = User::factory()->create(['type' => UserTypeEnum::ADMIN]);
    $policy = new MusicPolicy();

    expect($policy->viewAny($admin))->toBeTrue();
});

it('denies non-admin to view any music', function (): void {
    $user = User::factory()->create(['type' => UserTypeEnum::USER]);
    $policy = new MusicPolicy();

    expect($policy->viewAny($user))->toBeFalse();
});

it('allows admin to view a music', function (): void {
    $admin = User::factory()->create(['type' => UserTypeEnum::ADMIN]);
    $music = Music::factory()->create();
    $policy = new MusicPolicy();

    expect($policy->view($admin))->toBeTrue();
});

it('denies non-admin to view a music', function (): void {
    $user = User::factory()->create(['type' => UserTypeEnum::USER]);
    $music = Music::factory()->create();
    $policy = new MusicPolicy();

    expect($policy->view($user))->toBeFalse();
});

it('denies anyone to update music via policy', function (): void {
    $admin = User::factory()->create(['type' => UserTypeEnum::ADMIN]);
    $music = Music::factory()->create();
    $policy = new MusicPolicy();

    expect($policy->update())->toBeFalse();
});

it('denies anyone to create or delete music via policy', function (): void {
    $admin = User::factory()->create(['type' => UserTypeEnum::ADMIN]);
    $music = Music::factory()->create();
    $policy = new MusicPolicy();

    expect($policy->create())->toBeFalse()
        ->and($policy->delete())->toBeFalse();
});

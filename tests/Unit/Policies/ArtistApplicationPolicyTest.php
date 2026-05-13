<?php

declare(strict_types=1);

use App\Enums\UserTypeEnum;
use App\Models\ArtistApplication;
use App\Models\User;
use App\Policies\ArtistApplicationPolicy;

it('allows admin to view any artist application', function (): void {
    $admin = User::factory()->create(['type' => UserTypeEnum::ADMIN]);
    $policy = new ArtistApplicationPolicy();

    expect($policy->viewAny($admin))->toBeTrue();
});

it('denies non-admin to view any artist application', function (): void {
    $user = User::factory()->create(['type' => UserTypeEnum::USER]);
    $policy = new ArtistApplicationPolicy();

    expect($policy->viewAny($user))->toBeFalse();
});

it('allows admin to view an artist application', function (): void {
    $admin = User::factory()->create(['type' => UserTypeEnum::ADMIN]);
    $application = ArtistApplication::factory()->create();
    $policy = new ArtistApplicationPolicy();

    expect($policy->view($admin))->toBeTrue();
});

it('denies non-admin to view an artist application', function (): void {
    $user = User::factory()->create(['type' => UserTypeEnum::USER]);
    $application = ArtistApplication::factory()->create();
    $policy = new ArtistApplicationPolicy();

    expect($policy->view($user))->toBeFalse();
});

it('denies anyone to create artist application via policy', function (): void {
    $admin = User::factory()->create(['type' => UserTypeEnum::ADMIN]);
    $policy = new ArtistApplicationPolicy();

    expect($policy->create())->toBeFalse();
});

it('allows admin to update artist application', function (): void {
    $admin = User::factory()->create(['type' => UserTypeEnum::ADMIN]);
    $application = ArtistApplication::factory()->create();
    $policy = new ArtistApplicationPolicy();

    expect($policy->update($admin))->toBeTrue();
});

it('denies non-admin to update artist application', function (): void {
    $user = User::factory()->create(['type' => UserTypeEnum::USER]);
    $application = ArtistApplication::factory()->create();
    $policy = new ArtistApplicationPolicy();

    expect($policy->update($user))->toBeFalse();
});

it('denies anyone to delete artist application', function (): void {
    $admin = User::factory()->create(['type' => UserTypeEnum::ADMIN]);
    $application = ArtistApplication::factory()->create();
    $policy = new ArtistApplicationPolicy();

    expect($policy->delete())->toBeFalse();
});

it('denies anyone to restore or forceDelete artist application', function (): void {
    $admin = User::factory()->create(['type' => UserTypeEnum::ADMIN]);
    $application = ArtistApplication::factory()->create();
    $policy = new ArtistApplicationPolicy();

    expect($policy->restore())->toBeFalse()
        ->and($policy->forceDelete())->toBeFalse();
});

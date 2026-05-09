<?php

declare(strict_types=1);

use App\Enums\UserTypeEnum;
use App\Models\MusicRating;
use App\Models\User;
use App\Policies\MusicRatingPolicy;

it('allows admin to view any music rating', function (): void {
    $admin = User::factory()->create(['type' => UserTypeEnum::ADMIN]);
    $policy = new MusicRatingPolicy();

    expect($policy->viewAny($admin))->toBeTrue();
});

it('denies non-admin to view any music rating', function (): void {
    $user = User::factory()->create(['type' => UserTypeEnum::USER]);
    $policy = new MusicRatingPolicy();

    expect($policy->viewAny($user))->toBeFalse();
});

it('allows admin to view a music rating', function (): void {
    $admin = User::factory()->create(['type' => UserTypeEnum::ADMIN]);
    $rating = MusicRating::factory()->create();
    $policy = new MusicRatingPolicy();

    expect($policy->view($admin))->toBeTrue();
});

it('denies non-admin to view a music rating', function (): void {
    $user = User::factory()->create(['type' => UserTypeEnum::USER]);
    $rating = MusicRating::factory()->create();
    $policy = new MusicRatingPolicy();

    expect($policy->view($user))->toBeFalse();
});

it('denies anyone to update music rating via policy', function (): void {
    $admin = User::factory()->create(['type' => UserTypeEnum::ADMIN]);
    $rating = MusicRating::factory()->create();
    $policy = new MusicRatingPolicy();

    expect($policy->update())->toBeFalse();
});

it('denies anyone to create or delete music rating via policy', function (): void {
    $admin = User::factory()->create(['type' => UserTypeEnum::ADMIN]);
    $rating = MusicRating::factory()->create();
    $policy = new MusicRatingPolicy();

    expect($policy->create())->toBeFalse()
        ->and($policy->delete())->toBeFalse();
});

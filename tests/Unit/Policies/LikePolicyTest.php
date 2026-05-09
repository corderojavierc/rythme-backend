<?php

declare(strict_types=1);

use App\Enums\UserTypeEnum;
use App\Models\Like;
use App\Models\User;
use App\Policies\LikePolicy;

it('allows admin to view any like', function (): void {
    $admin = User::factory()->create(['type' => UserTypeEnum::ADMIN]);
    $policy = new LikePolicy();

    expect($policy->viewAny($admin))->toBeTrue();
});

it('denies non-admin to view any like', function (): void {
    $user = User::factory()->create(['type' => UserTypeEnum::USER]);
    $policy = new LikePolicy();

    expect($policy->viewAny($user))->toBeFalse();
});

it('denies anyone to create, update or delete likes via policy', function (): void {
    $admin = User::factory()->create(['type' => UserTypeEnum::ADMIN]);
    $like = Like::factory()->create();
    $policy = new LikePolicy();

    expect($policy->create())->toBeFalse()
        ->and($policy->update())->toBeFalse()
        ->and($policy->delete())->toBeFalse();
});

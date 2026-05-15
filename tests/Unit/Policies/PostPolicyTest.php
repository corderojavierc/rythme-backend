<?php

declare(strict_types=1);

use App\Enums\UserTypeEnum;
use App\Models\Post;
use App\Models\User;
use App\Policies\PostPolicy;

it('allows admin to view any post', function (): void {
    $admin = User::factory()->create(['type' => UserTypeEnum::ADMIN]);
    $policy = new PostPolicy();

    expect($policy->viewAny($admin))->toBeTrue();
});

it('denies non-admin to view any post', function (): void {
    $user = User::factory()->create(['type' => UserTypeEnum::USER]);
    $policy = new PostPolicy();

    expect($policy->viewAny($user))->toBeFalse();
});

it('allows admin to view a post', function (): void {
    $admin = User::factory()->create(['type' => UserTypeEnum::ADMIN]);
    $post = Post::factory()->create();
    $policy = new PostPolicy();

    expect($policy->view($admin))->toBeTrue();
});

it('denies non-admin to view a post', function (): void {
    $user = User::factory()->create(['type' => UserTypeEnum::USER]);
    $post = Post::factory()->create();
    $policy = new PostPolicy();

    expect($policy->view($user))->toBeFalse();
});

it('denies anyone to create post', function (): void {
    $admin = User::factory()->create(['type' => UserTypeEnum::ADMIN]);
    $policy = new PostPolicy();

    expect($policy->create())->toBeFalse();
});

it('denies anyone to update post', function (): void {
    $admin = User::factory()->create(['type' => UserTypeEnum::ADMIN]);
    $post = Post::factory()->create();
    $policy = new PostPolicy();

    expect($policy->update())->toBeFalse();
});

it('allows admin to delete post', function (): void {
    $admin = User::factory()->create(['type' => UserTypeEnum::ADMIN]);
    $post = Post::factory()->create();
    $policy = new PostPolicy();

    expect($policy->delete($admin))->toBeTrue();
});

it('denies non-admin to delete post', function (): void {
    $user = User::factory()->create(['type' => UserTypeEnum::USER]);
    $post = Post::factory()->create();
    $policy = new PostPolicy();

    expect($policy->delete($user))->toBeFalse();
});

it('denies anyone to restore or forceDelete post', function (): void {
    $admin = User::factory()->create(['type' => UserTypeEnum::ADMIN]);
    $post = Post::factory()->create();
    $policy = new PostPolicy();

    expect($policy->restore())->toBeFalse()
        ->and($policy->forceDelete())->toBeFalse();
});

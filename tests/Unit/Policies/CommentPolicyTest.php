<?php

declare(strict_types=1);

use App\Enums\UserTypeEnum;
use App\Models\Comment;
use App\Models\User;
use App\Policies\CommentPolicy;

it('allows admin to view any comment', function (): void {
    $admin = User::factory()->create(['type' => UserTypeEnum::ADMIN]);
    $policy = new CommentPolicy();

    expect($policy->viewAny($admin))->toBeTrue();
});

it('denies non-admin to view any comment', function (): void {
    $user = User::factory()->create(['type' => UserTypeEnum::USER]);
    $policy = new CommentPolicy();

    expect($policy->viewAny($user))->toBeFalse();
});

it('allows admin to view a comment', function (): void {
    $admin = User::factory()->create(['type' => UserTypeEnum::ADMIN]);
    $comment = Comment::factory()->create();
    $policy = new CommentPolicy();

    expect($policy->view($admin))->toBeTrue();
});

it('denies non-admin to view a comment', function (): void {
    $user = User::factory()->create(['type' => UserTypeEnum::USER]);
    $comment = Comment::factory()->create();
    $policy = new CommentPolicy();

    expect($policy->view($user))->toBeFalse();
});

it('denies anyone to create comment', function (): void {
    $admin = User::factory()->create(['type' => UserTypeEnum::ADMIN]);
    $policy = new CommentPolicy();

    expect($policy->create())->toBeFalse();
});

it('denies anyone to update comment', function (): void {
    $admin = User::factory()->create(['type' => UserTypeEnum::ADMIN]);
    $comment = Comment::factory()->create();
    $policy = new CommentPolicy();

    expect($policy->update())->toBeFalse();
});

it('allows admin to delete comment', function (): void {
    $admin = User::factory()->create(['type' => UserTypeEnum::ADMIN]);
    $comment = Comment::factory()->create();
    $policy = new CommentPolicy();

    expect($policy->delete($admin))->toBeTrue();
});

it('denies non-admin to delete comment', function (): void {
    $user = User::factory()->create(['type' => UserTypeEnum::USER]);
    $comment = Comment::factory()->create();
    $policy = new CommentPolicy();

    expect($policy->delete($user))->toBeFalse();
});

it('denies anyone to restore or forceDelete comment', function (): void {
    $admin = User::factory()->create(['type' => UserTypeEnum::ADMIN]);
    $comment = Comment::factory()->create();
    $policy = new CommentPolicy();

    expect($policy->restore())->toBeFalse()
        ->and($policy->forceDelete())->toBeFalse();
});

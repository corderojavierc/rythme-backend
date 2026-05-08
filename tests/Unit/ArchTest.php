<?php

declare(strict_types=1);

use App\Http\Controllers\Api\ArtistApplicationController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\MusicController;
use App\Http\Controllers\Api\PostController;
use App\Http\Controllers\Api\UserController;

arch()->preset()->php();
arch()->preset()->strict()->ignoring([
    'App\Filament',
    MusicController::class,
    PostController::class,
    UserController::class,
    ArtistApplicationController::class,
]);
arch()->preset()->laravel()->ignoring([
    'App\Providers\Filament',
    AuthController::class,
    MusicController::class,
    PostController::class,
    UserController::class,
    ArtistApplicationController::class,
]);
arch()->preset()->security()->ignoring([
    'assert',
    MusicController::class,
    PostController::class,
    UserController::class,
    ArtistApplicationController::class,
]);

arch('controllers')
    ->expect('App\Http\Controllers')
    ->not->toBeUsed();

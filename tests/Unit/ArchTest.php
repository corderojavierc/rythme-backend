<?php

declare(strict_types=1);

use App\Http\Controllers\ArtistApplicationController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\MusicController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\UserController;

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

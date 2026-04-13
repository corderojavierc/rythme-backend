<?php

declare(strict_types=1);

use App\Http\Controllers\AuthController;
use App\Http\Controllers\FollowController;
use App\Http\Controllers\LikeController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::post('register', [AuthController::class, 'register'])->name('register');
Route::post('login', [AuthController::class, 'login'])->name('login');

Route::middleware('auth:sanctum')->group(function (): void {
    Route::post('logout', [AuthController::class, 'logout'])->name('logout');
    Route::post('clear', [AuthController::class, 'clear'])->name('clear');
});

Route::get('posts', [PostController::class, 'index'])->name('posts.index');

Route::get('users', [UserController::class, 'index'])->name('users.index');

Route::get('follows/{id}', [FollowController::class, 'index'])->name('follows.index');

Route::post('follows', [FollowController::class, 'store'])->name('follows.store');
Route::delete('follows', [FollowController::class, 'destroy'])->name('follows.destroy');

Route::get('likes/{id}', [LikeController::class, 'index'])->name('likes.index');

Route::post('likes', [LikeController::class, 'store'])->name('likes.store');
Route::delete('likes', [LikeController::class, 'destroy'])->name('likes.destroy');

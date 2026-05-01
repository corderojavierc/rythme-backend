<?php

declare(strict_types=1);

use App\Http\Controllers\ArtistApplicationController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\FollowController;
use App\Http\Controllers\LikeController;
use App\Http\Controllers\MusicController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::post('register', [AuthController::class, 'register'])->name('register');
Route::post('login', [AuthController::class, 'login'])->name('login');

Route::middleware('auth:sanctum')->group(function (): void {
    Route::post('logout', [AuthController::class, 'logout'])->name('logout');
    Route::post('clear', [AuthController::class, 'clear'])->name('clear');

    Route::get('posts', [PostController::class, 'index'])->name('posts.index');
    Route::get('posts/followed', [PostController::class, 'getFollowedPosts'])->name('posts.followed');
    Route::post('posts', [PostController::class, 'store'])->name('posts.store');
    Route::delete('posts/{id}', [PostController::class, 'destroy'])->name('posts.destroy');
    Route::get('/posts/check/{music_id}', [PostController::class, 'checkPost']);
    Route::get('posts/{id}', [PostController::class, 'show'])->name('posts.show');

    Route::get('users', [UserController::class, 'index'])->name('users.index');
    Route::get('{id}/posts', [UserController::class, 'getPosts'])->name('users.posts');
    Route::get('{id}/comments', [UserController::class, 'getComments'])->name('users.comments');
    Route::get('{id}/likes', [UserController::class, 'getLiked'])->name('users.likes');

    Route::get('follows/{id}', [FollowController::class, 'index'])->name('follows.index');
    Route::post('follows', [FollowController::class, 'store'])->name('follows.store');
    Route::delete('follows', [FollowController::class, 'destroy'])->name('follows.destroy');

    Route::get('likes/{id}', [LikeController::class, 'index'])->name('likes.index');
    Route::post('likes', [LikeController::class, 'store'])->name('likes.store');
    Route::delete('likes', [LikeController::class, 'destroy'])->name('likes.destroy');

    Route::get('comments', [CommentController::class, 'index'])->name('comments.index');
    Route::post('comments', [CommentController::class, 'store'])->name('comments.store');
    Route::delete('comments/{id}', [CommentController::class, 'destroy'])->name('comments.destroy');
    Route::get('comments/{id}', [CommentController::class, 'show'])->name('comments.show');

    Route::get('music', [MusicController::class, 'index'])->name('music.index');
    Route::post('music', [MusicController::class, 'store'])->name('music.store');
    Route::delete('music/{id}', [MusicController::class, 'destroy'])->name('music.destroy');
    Route::get('music/{id}', [MusicController::class, 'show'])->name('music.show');
    Route::post('/music/search', [MusicController::class, 'search'])->name('api.music.search');
    Route::get('music/{id}/posts', [MusicController::class, 'getPosts'])->name('music.posts');

    Route::get('artist-applications', [ArtistApplicationController::class, 'index'])->name('artist-applications.index');
    Route::post('artist-applications', [ArtistApplicationController::class, 'store'])->name('artist-applications.store');
    Route::get('artist-applications/has', [ArtistApplicationController::class, 'hasApplication'])->name('artist-applications.has');
});

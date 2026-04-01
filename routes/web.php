<?php

declare(strict_types=1);

use App\Http\Controllers\MusicController;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Route;

Route::get('/', fn (): Factory|View => view('welcome'));

// Vista del buscador
Route::get('/search-music', [MusicController::class, 'index'])->name('music.search.index');

// API
Route::prefix('api/music')->group(function (): void {
    Route::get('/spotify-search', [MusicController::class, 'searchSpotify'])->name('api.music.spotify.search');
    Route::post('/save-track', [MusicController::class, 'saveTrack'])->name('api.music.save-track');
});

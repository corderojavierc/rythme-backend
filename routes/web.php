<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MusicController;

Route::get('/', function () { return view('welcome'); });

// Vista del buscador
Route::get('/search-music', [MusicController::class, 'index'])->name('music.search.index');

// API
Route::prefix('api/music')->group(function () {
    Route::get('/spotify-search', [MusicController::class, 'searchSpotify'])->name('api.music.spotify.search');
    Route::post('/save-track', [MusicController::class, 'saveTrack'])->name('api.music.save-track');
});

<?php

use App\Http\Controllers\MusicController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return ['Laravel' => app()->version()];
});

Route::get('/hello', function () {
    return response()->json([
        'hello' => 'hello'
    ]);
});

Route::get('/music/generate', [
    MusicController::class,
    'generateMusic'
]);

require __DIR__ . '/auth.php';

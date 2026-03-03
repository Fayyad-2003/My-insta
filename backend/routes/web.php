<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return ['Laravel' => app()->version()];
});

Route::get('/hello', function () {
    return response()->json([
        'hello' => 'hello'
    ]);
});

require __DIR__ . '/auth.php';

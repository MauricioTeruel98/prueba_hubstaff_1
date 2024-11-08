<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Cache;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::get('/hubstaff/check-connection', function () {
    return response()->json([
        'connected' => Cache::has('hubstaff_access_token')
    ]);
});

<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\HubstaffTaskController;
use App\Http\Controllers\HubstaffAuthController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::get('/tasks/create', [HubstaffTaskController::class, 'create'])->name('tasks.create');
    Route::post('/tasks', [HubstaffTaskController::class, 'store'])->name('tasks.store');
    Route::get('/tasks', [HubstaffTaskController::class, 'index'])->name('tasks.index');
    Route::get('/hubstaff/connect', [HubstaffAuthController::class, 'redirect'])
        ->name('hubstaff.connect')
        ->middleware('web');
    Route::get('/hubstaff/callback', [HubstaffAuthController::class, 'callback'])
        ->name('hubstaff.callback')
        ->middleware('web');
    Route::post('/hubstaff/refresh-token', [HubstaffAuthController::class, 'refreshToken'])->name('hubstaff.refresh');
});

require __DIR__.'/auth.php';

<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\HubstaffTaskController;
use App\Http\Controllers\HubstaffAuthController;
use App\Http\Controllers\HubstaffProjectController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Illuminate\Support\Facades\Cache;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::get('/dashboard', function () {
    $hubstaffService = app(App\Services\HubstaffService::class);
    $userData = null;
    $organizationData = null;
    
    try {
        if (Cache::has('hubstaff_access_token')) {
            $userData = $hubstaffService->getUserInfo();
            $organizationData = $hubstaffService->getOrganizationInfo();
        }
    } catch (\Exception $e) {
        // Si hay error, simplemente dejamos los datos como null
    }

    return Inertia::render('Dashboard', [
        'hubstaffUser' => $userData,
        'hubstaffOrganization' => $organizationData
    ]);
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
    Route::get('/projects', [HubstaffProjectController::class, 'index'])->name('projects.index');
    Route::get('/projects/{id}', [HubstaffProjectController::class, 'show'])->name('projects.show');
});

require __DIR__.'/auth.php';

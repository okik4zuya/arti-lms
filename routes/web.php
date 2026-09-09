<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ContentController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProgressController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route(auth()->check() ? 'dashboard' : 'login');
});

Route::middleware('guest')->group(function () {
    Route::get('login', [AuthController::class, 'create'])->name('login');
    Route::post('login', [AuthController::class, 'store']);
});

Route::post('logout', [AuthController::class, 'destroy'])
    ->middleware('auth')->name('logout');

Route::get('dashboard', [DashboardController::class, 'index'])
    ->middleware('auth')->name('dashboard');

Route::middleware(['auth', 'course.access'])->prefix('learn/{course}')->group(function () {
    Route::get('{slug}', [ContentController::class, 'show'])->name('content.show');
    Route::get('{slug}/images/{file}', [ContentController::class, 'image'])->name('content.image');
    Route::post('{slug}/progress', [ProgressController::class, 'store'])->name('progress.store');
});

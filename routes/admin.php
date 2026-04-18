<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\SettingsController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\MediaController;
use App\Http\Controllers\Admin\TwoFactorController;
use App\Http\Controllers\Admin\NewsPostController;
use App\Http\Controllers\Admin\AnalyticsController;
use App\Http\Controllers\Admin\EventController;

Route::prefix('events')->group(function () {

    Route::get('/', [EventController::class, 'index'])->name('admin.events.index');

    Route::post('/', [EventController::class, 'store'])->name('admin.events.store');

    Route::put('/{event}', [EventController::class, 'update'])->name('admin.events.update');

    Route::post('/{event}/publish', [EventController::class, 'publish'])->name('admin.events.publish');

    Route::post('/{event}/archive', [EventController::class, 'archive'])->name('admin.events.archive');

});

Route::get('/', [DashboardController::class, 'index'])->name('admin.dashboard');
Route::get('/dashboard', [DashboardController::class, 'index']);

Route::get('/settings', [SettingsController::class, 'index'])->name('admin.settings.index');
Route::post('/settings', [SettingsController::class, 'update'])->name('admin.settings.update');

Route::middleware(['role:Super Admin|Editor'])->group(function () {
    Route::resource('categories', CategoryController::class)->names('admin.categories');
});

Route::resource('media', MediaController::class)->names('admin.media')->except(['show', 'edit', 'update']);

Route::get('/2fa', [TwoFactorController::class, 'index'])->name('admin.2fa.index');
Route::post('/2fa', [TwoFactorController::class, 'verify'])->name('admin.2fa.verify');

Route::middleware(['role:Super Admin|Editor|Editorial Lead|Director'])->group(function () {
    Route::resource('news', NewsPostController::class)->names('admin.news');
});

Route::middleware(['role:Director|Ops Head|Super Admin'])->group(function () {
    Route::get('/analytics', [AnalyticsController::class, 'index'])->name('admin.analytics');
});

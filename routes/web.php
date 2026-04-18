<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\NewsController;
use App\Http\Controllers\FeedController;
use App\Http\Controllers\EventController;

/* PUBLIC ROUTES */

Route::get('/', [HomeController::class, 'index']);

Route::get('/news/{slug}', [NewsController::class, 'show'])->name('news.show');

Route::get('/rss', [FeedController::class, 'rss'])->name('rss');
Route::get('/sitemap.xml', [FeedController::class, 'sitemap'])->name('sitemap');

Route::get('/events', [EventController::class, 'index'])->name('events.index');
Route::get('/events/{slug}', [EventController::class, 'show'])->name('events.show');

/* AUTH */

Route::prefix('admin')->group(function () {
Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';

/* ADMIN */

Route::middleware(['auth'])->prefix('admin')->group(function () {

    Route::get('/theme', [SettingController::class, 'editTheme']);
    Route::post('/theme', [SettingController::class, 'updateTheme']);

    Route::post('/theme/sections', [SettingController::class, 'updateSections'])
        ->name('admin.theme.sections.update');

Route::post('/theme/settings', [SettingController::class, 'updateSectionSettings'])
    ->name('admin.theme.settings.update');

});

<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\NewsController;
use App\Http\Controllers\FeedController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\Organizer\OrganizerEventController;
use App\Http\Controllers\Admin\EventController as AdminEventController;

/* PUBLIC ROUTES */
Route::get('/', [HomeController::class, 'index']);
Route::get('/news', [NewsController::class, 'index'])->name('news.index');
Route::get('/news/{slug}', [NewsController::class, 'show'])->name('news.show');
Route::get('/rss', [FeedController::class, 'rss'])->name('rss');
Route::get('/sitemap.xml', [FeedController::class, 'sitemap'])->name('sitemap');
Route::get('/events', [EventController::class, 'index'])->name('events.index');
Route::get('/events/{slug}', [EventController::class, 'show'])->name('events.show');

/* AUTH */
require __DIR__.'/auth.php';

/* ADMIN */
Route::middleware(['auth'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::get('/theme', [SettingController::class, 'editTheme'])->name('theme.edit');
    Route::post('/theme', [SettingController::class, 'updateTheme'])->name('theme.update');
    Route::post('/theme/sections', [SettingController::class, 'updateSections'])->name('theme.sections.update');
    Route::post('/theme/settings', [SettingController::class, 'updateSectionSettings'])->name('theme.settings.update');

    // Events management
    Route::get('/events', [AdminEventController::class, 'index'])->name('events.index');
    Route::post('/events', [AdminEventController::class, 'store'])->name('events.store');
    Route::put('/events/{event}', [AdminEventController::class, 'update'])->name('events.update');
    Route::post('/events/{event}/verify', [AdminEventController::class, 'verify'])->name('events.verify');
    Route::post('/events/{event}/publish', [AdminEventController::class, 'publish'])->name('events.publish');
    Route::post('/events/{event}/archive', [AdminEventController::class, 'archive'])->name('events.archive');
});

/* ORGANIZER */
Route::middleware(['auth'])->prefix('organizer')->name('organizer.')->group(function () {
    Route::get('/dashboard', [OrganizerEventController::class, 'dashboard'])->name('dashboard');
    Route::get('/events', [OrganizerEventController::class, 'index'])->name('events.index');
    Route::get('/events/create', [OrganizerEventController::class, 'create'])->name('events.create');
    Route::post('/events', [OrganizerEventController::class, 'store'])->name('events.store');
    Route::get('/events/{event}/edit', [OrganizerEventController::class, 'edit'])->name('events.edit');
    Route::put('/events/{event}', [OrganizerEventController::class, 'update'])->name('events.update');
    Route::get('/events/{event}/attendees', [OrganizerEventController::class, 'attendees'])->name('events.attendees');
    Route::post('/events/{event}/submit', [OrganizerEventController::class, 'submitForReview'])->name('events.submit');
});

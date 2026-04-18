<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Organizer\OrganizerEventController;

Route::get('/dashboard', [OrganizerEventController::class, 'dashboard'])
    ->name('organizer.dashboard');

Route::get('/events', [OrganizerEventController::class, 'index'])
    ->name('organizer.events.index');

Route::get('/events/create', [OrganizerEventController::class, 'create'])
    ->name('organizer.events.create');

Route::post('/events', [OrganizerEventController::class, 'store'])
    ->name('organizer.events.store');

Route::get('/events/{event}/edit', [OrganizerEventController::class, 'edit'])
    ->name('organizer.events.edit');

Route::put('/events/{event}', [OrganizerEventController::class, 'update'])
    ->name('organizer.events.update');

Route::get('/events/{event}/attendees', [OrganizerEventController::class, 'attendees'])
    ->name('organizer.events.attendees');

Route::post('/events/{event}/submit', [OrganizerEventController::class, 'submitForReview'])
    ->name('organizer.events.submit');

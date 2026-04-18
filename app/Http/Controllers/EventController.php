<?php

namespace App\Http\Controllers;

use App\Models\Event;

class EventController extends Controller
{
    public function index()
    {
        return Event::published()
            ->upcoming()
            ->orderBy('start_datetime', 'asc')
            ->paginate(10);
    }

    public function show($slug)
    {
        return Event::published()
            ->where('slug', $slug)
            ->firstOrFail();
    }
}

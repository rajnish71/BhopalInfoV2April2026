<?php

namespace App\Http\Controllers;

use App\Models\Event;

class EventController extends Controller
{
    public function index()
    {
        $events = Event::published()
            ->upcoming()
            ->orderBy('start_datetime', 'asc')
            ->paginate(10);

        return view('events.index', compact('events'));
    }

    public function show($slug)
    {
        $event = Event::published()
            ->where('slug', $slug)
            ->firstOrFail();

        return view('events.show', compact('event'));
    }
}

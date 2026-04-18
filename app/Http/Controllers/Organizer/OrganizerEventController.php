<?php

namespace App\Http\Controllers\Organizer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class OrganizerEventController extends Controller
{

    public function dashboard()
    {
        return view('organizer.dashboard');
    }

    public function index()
    {
        return view('organizer.events.index');
    }

    public function create()
    {
        return view('organizer.events.create');
    }

    public function store(Request $request)
    {
        // Temporary stub until event model logic is wired
        return redirect()->route('organizer.events.index')
            ->with('success', 'Event created (temporary stub)');
    }

    public function edit($event)
    {
        return view('organizer.events.edit', compact('event'));
    }

    public function update(Request $request, $event)
    {
        return redirect()->route('organizer.events.index')
            ->with('success', 'Event updated (temporary stub)');
    }

    public function attendees($event)
    {
        return view('organizer.events.attendees', compact('event'));
    }

    public function submitForReview($event)
    {
        return redirect()->route('organizer.events.index')
            ->with('success', 'Event submitted for review');
    }

}

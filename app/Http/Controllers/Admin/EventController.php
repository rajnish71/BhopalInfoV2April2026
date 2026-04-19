<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\EventStatusLog;
use Illuminate\Http\Request;

class EventController extends Controller
{
    public function index()
    {
        $events = Event::with(['category', 'organizer'])
            ->latest()
            ->paginate(20);

        return view('admin.events.index', compact('events'));
    }

    public function store(Request $request)
    {
        $this->authorize('create', Event::class);

        $validated = $request->validate([
            'city_id'           => 'required|exists:cities,id',
            'event_category_id' => 'required|exists:event_categories,id',
            'organizer_id'      => 'required|exists:event_organizers,id',
            'title'             => 'required|string|max:255',
            'description'       => 'required|string',
            'venue_name'        => 'required|string|max:255',
            'start_datetime'    => 'required|date|after_or_equal:now',
            'end_datetime'      => 'nullable|date|after:start_datetime',
        ]);

        $validated['created_by']          = auth()->id();
        $validated['publish_status']      = 'draft';
        $validated['verification_status'] = 'pending';

        $event = Event::create($validated);

        EventStatusLog::create([
            'event_id'     => $event->id,
            'action'       => 'created',
            'performed_by' => auth()->id(),
        ]);

        return redirect()->route('admin.events.index')
            ->with('success', 'Event created.');
    }

    public function update(Request $request, Event $event)
    {
        $this->authorize('update', $event);

        if ($event->publish_status === 'published') {
            abort(403, 'Published events cannot be edited.');
        }

        $validated = $request->validate([
            'title'          => 'sometimes|string|max:255',
            'description'    => 'sometimes|string',
            'venue_name'     => 'sometimes|string|max:255',
            'start_datetime' => 'sometimes|date',
            'end_datetime'   => 'nullable|date|after:start_datetime',
        ]);

        $event->update($validated);

        EventStatusLog::create([
            'event_id'     => $event->id,
            'action'       => 'updated',
            'performed_by' => auth()->id(),
        ]);

        return redirect()->route('admin.events.index')
            ->with('success', 'Event updated.');
    }

    public function verify(Event $event)
    {
        $this->authorize('verify', $event);

        $event->update(['verification_status' => 'verified']);

        EventStatusLog::create([
            'event_id'     => $event->id,
            'action'       => 'verified',
            'performed_by' => auth()->id(),
        ]);

        return redirect()->route('admin.events.index')
            ->with('success', 'Event verified.');
    }

    public function publish(Event $event)
    {
        $this->authorize('publish', $event);

        if ($event->publish_status !== 'review') {
            return back()->with('error', 'Only events under review can be published.');
        }

        if ($event->verification_status !== 'verified') {
            return back()->with('error', 'Event must be verified before publishing.');
        }

        $event->update([
            'publish_status' => 'published',
            'approved_by'    => auth()->id(),
        ]);

        EventStatusLog::create([
            'event_id'     => $event->id,
            'action'       => 'published',
            'performed_by' => auth()->id(),
        ]);

        return redirect()->route('admin.events.index')
            ->with('success', 'Event published.');
    }

    public function archive(Event $event)
    {
        $this->authorize('archive', $event);

        $event->update(['publish_status' => 'archived']);

        EventStatusLog::create([
            'event_id'     => $event->id,
            'action'       => 'archived',
            'performed_by' => auth()->id(),
        ]);

        return redirect()->route('admin.events.index')
            ->with('success', 'Event archived.');
    }
}

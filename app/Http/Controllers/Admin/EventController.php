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
        return Event::with(['category', 'organizer'])
            ->latest()
            ->paginate(20);
    }

    public function store(Request $request)
    {
        $this->authorize('create', Event::class);

        $validated = $request->validate([
            'city_id' => 'required|exists:cities,id',
            'event_category_id' => 'required|exists:event_categories,id',
            'organizer_id' => 'required|exists:event_organizers,id',
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'venue_name' => 'required|string|max:255',
            'start_datetime' => 'required|date|after_or_equal:now',
            'end_datetime' => 'nullable|date|after:start_datetime',
        ]);

        $validated['created_by'] = auth()->id();
        $validated['publish_status'] = 'draft';
        $validated['verification_status'] = 'pending';

        $event = Event::create($validated);

        EventStatusLog::create([
            'event_id' => $event->id,
            'action' => 'created',
            'performed_by' => auth()->id(),
        ]);

        return $event;
    }

    public function update(Request $request, Event $event)
    {
        $this->authorize('update', $event);

        if ($event->publish_status === 'published') {
            abort(403, 'Published events cannot be edited.');
        }

        $validated = $request->validate([
            'title' => 'sometimes|string|max:255',
            'description' => 'sometimes|string',
            'venue_name' => 'sometimes|string|max:255',
            'start_datetime' => 'sometimes|date',
            'end_datetime' => 'nullable|date|after:start_datetime',
        ]);

        $event->update($validated);

        EventStatusLog::create([
            'event_id' => $event->id,
            'action' => 'updated',
            'performed_by' => auth()->id(),
        ]);

        return $event;
    }

    public function submit(Event $event)
    {
        $this->authorize('update', $event);

        if ($event->publish_status !== 'draft') {
            abort(422, 'Only draft events can be submitted.');
        }

        $event->update([
            'publish_status' => 'review',
        ]);

        EventStatusLog::create([
            'event_id' => $event->id,
            'action' => 'submitted',
            'performed_by' => auth()->id(),
        ]);

        return response()->json(['message' => 'Event submitted for review']);
    }

    public function publish(Event $event)
    {
        $this->authorize('publish', $event);

        if ($event->publish_status !== 'review') {
            abort(422, 'Only events under review can be published.');
        }

        if ($event->verification_status !== 'verified') {
            abort(422, 'Event must be verified before publishing.');
        }

        $event->update([
            'publish_status' => 'published',
            'approved_by' => auth()->id(),
        ]);

        EventStatusLog::create([
            'event_id' => $event->id,
            'action' => 'published',
            'performed_by' => auth()->id(),
        ]);

        return response()->json(['message' => 'Event published']);
    }

    public function archive(Event $event)
    {
        $this->authorize('archive', $event);

        if ($event->publish_status !== 'published') {
            abort(422, 'Only published events can be archived.');
        }

        $event->update([
            'publish_status' => 'archived',
        ]);

        EventStatusLog::create([
            'event_id' => $event->id,
            'action' => 'archived',
            'performed_by' => auth()->id(),
        ]);

        return response()->json(['message' => 'Event archived']);
    }
}

<?php

namespace App\Observers;

use App\Models\Event;
use App\Models\EventStatusLog;
use Illuminate\Support\Str;

class EventObserver
{
    public function creating(Event $event): void
    {
        if (empty($event->slug)) {
            $event->slug = Str::slug($event->title) . '-' . uniqid();
        }
    }

    public function created(Event $event): void
    {
        EventStatusLog::create([
            'event_id' => $event->id,
            'action' => 'created',
            'performed_by' => auth()->id() ?? $event->created_by,
        ]);
    }

    public function updating(Event $event): void
    {
        if ($event->isDirty('status')) {

            $action = match ($event->status) {
                'review' => 'submitted',
                'published' => 'published',
                'archived' => 'archived',
                default => 'updated',
            };

            EventStatusLog::create([
                'event_id' => $event->id,
                'action' => $action,
                'performed_by' => auth()->id() ?? $event->created_by,
            ]);
        }
    }
}

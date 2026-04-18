<?php

namespace App\Policies;

use App\Models\Event;
use App\Models\User;

class EventPolicy
{
    public function view(?User $user, Event $event): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return $user !== null;
    }

    public function update(User $user, Event $event): bool
    {
        // Creator or Director can update
        if ($user->id === $event->created_by || $user->hasRole('Director')) {
            // Cannot edit archived
            return $event->publish_status !== 'archived';
        }

        return false;
    }

    public function publish(User $user, Event $event): bool
    {
        // Only Director or Editorial Lead
        if (!$user->hasAnyRole(['Director', 'Editorial Lead'])) {
            return false;
        }

        // Only review-stage events can be published
        return $event->publish_status === 'review';
    }

    public function archive(User $user, Event $event): bool
    {
        if (!$user->hasAnyRole(['Director', 'Editorial Lead'])) {
            return false;
        }

        return $event->publish_status === 'published';
    }

    public function delete(User $user, Event $event): bool
    {
        // Hard delete disabled — archive only
        return false;
    }
}

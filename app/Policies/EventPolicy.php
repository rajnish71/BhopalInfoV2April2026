<?php

namespace App\Policies;

use App\Models\Event;
use App\Models\User;

class EventPolicy
{
    private function isSuperAdmin(User $user): bool
    {
        return $user->hasRole('super-admin');
    }

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
        if ($this->isSuperAdmin($user)) {
            return $event->publish_status !== 'archived';
        }

        if ($user->id === $event->created_by) {
            return $event->publish_status !== 'archived';
        }

        return false;
    }

    public function verify(User $user, Event $event): bool
    {
        return $this->isSuperAdmin($user);
    }

    public function publish(User $user, Event $event): bool
    {
        if (!$this->isSuperAdmin($user)) {
            return false;
        }

        return $event->publish_status === 'review'
            && $event->verification_status === 'verified';
    }

    public function archive(User $user, Event $event): bool
    {
        if (!$this->isSuperAdmin($user)) {
            return false;
        }

        return $event->publish_status === 'published';
    }

    public function delete(User $user, Event $event): bool
    {
        return false;
    }
}

<?php
namespace App\Observers;

use App\Models\User;
use Illuminate\Support\Facades\Log;

class UserObserver
{
    public function updating(User $user)
    {
        if ($user->isDirty('role_id') || $user->isDirty('role')) {
             if (!auth()->user()->hasRole('Director')) {
                 abort(403, 'Unauthorized: Role escalation detected.');
             }
             // Log role change
             Log::info("User " . auth()->id() . " changed role for User " . $user->id . " from " . $user->getOriginal('role_id') . " to " . $user->role_id);
        }

        // Prevent self update
        if (auth()->id() === $user->id && ($user->isDirty('role_id'))) {
            abort(403, 'Unauthorized: Cannot change your own role.');
        }
    }
}
<?php
namespace App\Policies;

use App\Models\NewsPost;
use App\Models\User;

class NewsPostPolicy
{
    public function viewAny(User $user) { return true; }
    public function view(User $user, NewsPost $post) { return true; }
    public function create(User $user) { return $user->hasAnyRole(['Super Admin', 'Editor', 'Editorial Lead', 'Director']); }
    
    public function update(User $user, NewsPost $post) {
        if ($post->publish_status === 'published' && !$user->hasAnyRole(['Super Admin', 'Editorial Lead', 'Director'])) {
            return false;
        }
        return true;
    }

    public function delete(User $user, NewsPost $post) {
        if ($post->publish_status === 'published') return false;
        return $user->hasAnyRole(['Super Admin', 'Director']);
    }

    public function verify(User $user, NewsPost $post) {
        return $user->hasAnyRole(['Super Admin', 'Editorial Lead', 'Director']);
    }

    public function publish(User $user, NewsPost $post) {
        return $user->hasAnyRole(['Super Admin', 'Editorial Lead', 'Director']);
    }

    public function archive(User $user, NewsPost $post) {
        return $user->hasAnyRole(['Super Admin', 'Editorial Lead', 'Director']);
    }

    public function markCritical(User $user, NewsPost $post) {
        return $user->hasAnyRole(['Super Admin', 'Editorial Lead', 'Director']);
    }
}
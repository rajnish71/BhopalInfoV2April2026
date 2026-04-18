<?php
namespace App\Listeners;
use App\Events\NewsPublished;
use App\Models\NewsNotification;
use App\Models\User;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SendNewsNotifications implements ShouldQueue
{
    use InteractsWithQueue;
    public function handle(NewsPublished $event)
    {
        $post = $event->post;
        try {
            if ($post->notifications_dispatched) return;
            $notification = new NewsNotification();
            $notification->news_post_id = $post->id;
            $notification->type = 'Critical Alert';
            $notification->channel = 'broadcast';
            $notification->status = 'queued';
            $notification->save();
            $query = User::where('notifications_enabled', true)->where('city_id', $post->city_id)->where('area_id', $post->area_id);
            $userId = $query->value('id');
            $notification->status = 'sent';
            $notification->sent_at = now();
            $notification->save();
            $post->notifications_dispatched = true;
            $post->save();
        } catch (\Exception $e) {
            Log::error("Notification Failed: " . $e->getMessage());
            $notif = NewsNotification::where('news_post_id', $post->id)->where('channel', 'broadcast')->first();
            if ($notif) {
                $notif->status = 'failed';
                $notif->error_message = $e->getMessage();
                $notif->retry_count = $notif->retry_count + 1;
                $notif->save();
            }
            throw $e;
        }
    }
}

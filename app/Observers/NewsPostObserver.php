<?php
namespace App\Observers;

use App\Models\NewsPost;
use Illuminate\Support\Facades\Cache;

class NewsPostObserver
{
    public function saved(NewsPost $post)
    {
        $this->invalidate($post);
    }

    public function deleted(NewsPost $post)
    {
        $this->invalidate($post);
    }

    public function restored(NewsPost $post)
    {
        $this->invalidate($post);
    }

    protected function invalidate(NewsPost $post)
    {
        $cityId = $post->city_id ?? 1;
        
        // Invalidate Critical Alert Cache
        Cache::forget("critical_alert_{$cityId}");
        
        // Invalidate Area Feed Cache (Specific area and 'all')
        Cache::forget("homepage_feed_{$cityId}_all");
        if ($post->area_id) {
            Cache::forget("homepage_feed_{$cityId}_{$post->area_id}");
        }
    }
}
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\NewsPost;
use App\Models\Event;

class HomeController extends Controller
{
    public function index()
    {
        // 1. Fetch highest priority news for Hero
        $heroPrimary = NewsPost::where('publish_status', 'published')
            ->whereNotNull('published_at')
            ->orderByDesc('priority')
            ->orderByDesc('is_alert')
            ->orderByDesc('published_at')
            ->first();

        // 2. Fetch next 3 high priority news for Secondary Hero
        $heroSecondary = NewsPost::where('publish_status', 'published')
            ->whereNotNull('published_at')
            ->orderByDesc('priority')
            ->orderByDesc('is_alert')
            ->orderByDesc('published_at')
            ->skip(1)
            ->take(3)
            ->get();

        // 3. Fetch latest news list
        $news = NewsPost::where('publish_status', 'published')
            ->whereNotNull('published_at')
            ->orderByDesc('priority')
            ->orderByDesc('is_alert')
            ->orderByDesc('published_at')
            ->take(10)
            ->get();

        // 4. Fetch upcoming published events
        $upcomingEvents = Event::where('publish_status', 'published')
            ->where('start_datetime', '>=', now())
            ->orderBy('start_datetime', 'asc')
            ->take(5)
            ->get();

        return view(theme_view('pages.home'), compact(
            'heroPrimary',
            'heroSecondary',
            'news',
            'upcomingEvents'
        ));
    }
}

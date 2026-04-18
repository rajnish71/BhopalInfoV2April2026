@extends(theme_view('layouts.app'))

@section('content')

    @php
        $sections = theme_sections('home');
        
        $heroPrimary = \App\Models\NewsPost::where('publish_status', 'published')
            ->whereNotNull('published_at')
            ->orderByDesc('priority')
            ->orderByDesc('is_alert')
            ->orderByDesc('published_at')
            ->first();

        $heroSecondary = \App\Models\NewsPost::where('publish_status', 'published')
            ->whereNotNull('published_at')
            ->orderByDesc('priority')
            ->orderByDesc('is_alert')
            ->orderByDesc('published_at')
            ->skip(1)
            ->take(3)
            ->get();

        $news = \App\Models\NewsPost::where('publish_status', 'published')
            ->whereNotNull('published_at')
            ->orderByDesc('priority')
            ->orderByDesc('is_alert')
            ->orderByDesc('published_at')
            ->take(5)
            ->get();

        $upcomingEvents = \App\Models\Event::where('publish_status', 'published')
            ->where('start_datetime', '>=', now())
            ->orderBy('start_datetime')
            ->take(5)
            ->get();
    @endphp

    @foreach($sections as $section)

        @includeIf(
            'themes.' . active_theme() . '.sections.' . $section,
            [
                'heroPrimary' => $heroPrimary,
                'heroSecondary' => $heroSecondary,
                'news' => $news,
                'upcomingEvents' => $upcomingEvents,
            ]
        )

    @endforeach

@endsection

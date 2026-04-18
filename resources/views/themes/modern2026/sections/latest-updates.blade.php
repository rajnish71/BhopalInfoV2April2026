@php
$limit = section_setting('latest-updates', 'limit', 5);

$news = \App\Models\NewsPost::where('publish_status', 'published')
    ->whereNotNull('published_at')
    ->orderByDesc('priority')
    ->orderByDesc('is_alert')
    ->orderByDesc('published_at')
    ->take($limit)
    ->get();
@endphp

<div style="display:flex; gap:30px;">

    {{-- LEFT: NEWS --}}
    <div style="flex:2;">
        <h2 style="margin-bottom:12px;">Latest Updates</h2>

        @foreach($news as $item)
        <div style="border:1px solid #eee; padding:12px; margin-bottom:12px; border-radius:8px;">
            
            <div style="font-size:11px; color:#888;">
                {{ $item->category->name ?? 'General' }} • {{ optional($item->published_at)->diffForHumans() }}
            </div>

            <div style="font-weight:600;">
                {{ $item->title }}
            </div>

            <div style="font-size:12px; color:#666;">
                {{ \Illuminate\Support\Str::limit($item->excerpt, 100) }}
            </div>

        </div>
        @endforeach
    </div>

{{-- RIGHT: EVENTS --}}
<div style="flex:1;">
    <h2 style="margin-bottom:12px;">Events in Bhopal</h2>

    @php
    $eventLimit = section_setting('events', 'limit', 5);

    $upcomingEvents = \App\Models\Event::where('publish_status', 'published')
        ->where('start_datetime', '>=', now())
        ->orderBy('start_datetime')
        ->take($eventLimit)
        ->get();
    @endphp

    @foreach($upcomingEvents as $event)
    <div style="border:1px solid #eee; padding:12px; margin-bottom:12px; border-radius:8px;">

        <div style="font-size:11px;">
            📅 {{ \Carbon\Carbon::parse($event->start_datetime)->format('d M, h:i A') }}
        </div>

        <div style="font-weight:600;">
            {{ $event->title }}
        </div>

        <div style="font-size:12px; color:#666;">
            {{ $event->venue ?? 'Bhopal' }}
        </div>

    </div>
    @endforeach

</div>
</div>

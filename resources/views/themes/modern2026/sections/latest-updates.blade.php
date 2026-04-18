<div style="display:flex; gap:30px;">

    {{-- LEFT: NEWS --}}
    <div style="flex:2;">
        <h2 style="margin-bottom:12px;">Latest Updates</h2>

        @if($news && count($news))
            @foreach($news as $item)
            <div style="border:1px solid #eee; padding:12px; margin-bottom:12px; border-radius:8px;">
                
                <div style="font-size:11px; color:#888;">
                    {{ $item->category->name ?? 'General' }} • {{ optional($item->published_at)->diffForHumans() }}
                </div>

                <div style="font-weight:600;">
                    <a href="/news/{{ $item->slug ?? $item->id }}" style="text-decoration:none; color:inherit;">
                        {{ $item->title }}
                    </a>
                </div>

                <div style="font-size:12px; color:#666;">
                    {{ \Illuminate\Support\Str::limit($item->excerpt ?? $item->summary, 100) }}
                </div>

            </div>
            @endforeach
        @else
            <div style="color:#888;">No recent news available.</div>
        @endif
    </div>

    {{-- RIGHT: EVENTS --}}
    <div style="flex:1;">
        <h2 style="margin-bottom:12px;">Events in Bhopal</h2>

        @if($upcomingEvents && count($upcomingEvents))
            @foreach($upcomingEvents as $event)
            <div style="border:1px solid #eee; padding:12px; margin-bottom:12px; border-radius:8px;">

                <div style="font-size:11px;">
                    📅 {{ \Carbon\Carbon::parse($event->start_datetime)->format('d M, h:i A') }}
                </div>

                <div style="font-weight:600;">
                    <a href="/events/{{ $event->slug ?? $event->id }}" style="text-decoration:none; color:inherit;">
                        {{ $event->title }}
                    </a>
                </div>

                <div style="font-size:12px; color:#666;">
                    📍 {{ $event->location_name ?? $event->venue ?? 'Bhopal' }}
                </div>

            </div>
            @endforeach
        @else
            <div style="color:#888;">No upcoming events at this time.</div>
        @endif

    </div>
</div>

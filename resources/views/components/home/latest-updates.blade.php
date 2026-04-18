@if($news && count($news))
<div style="display:flex; gap:40px;">

    {{-- LEFT SIDE: NEWS LIST --}}
    <div style="flex:2;">
        <h2 style="margin-bottom:10px;">Latest Updates</h2>

        @foreach($news as $item)
        <div style="border:1px solid #eee; padding:16px; border-radius:8px; margin-bottom:15px;">

            <div style="font-size:11px; color:#888;">
                {{ $item->category?->name }} • {{ $item->published_at?->diffForHumans() }}
            </div>

            <div style="font-weight:600;">
                {{ $item->title }}
            </div>

            <div style="font-size:13px; color:#555;">
                {{ $item->summary }}
            </div>

        </div>
        @endforeach

    </div>

    {{-- RIGHT SIDE: EVENTS --}}
    <div style="flex:1;">
        <h2 style="margin-bottom:10px;">Events in Bhopal</h2>

        @foreach($events as $event)
        <a href="/events/{{ $event->slug }}" style="text-decoration:none; color:inherit;">
            <div style="border:1px solid #eee; padding:12px; border-radius:8px; margin-bottom:12px;">

                <div style="font-size:11px; color:#888;">
                    📅 {{ \Carbon\Carbon::parse($event->start_datetime)->format('d M, h:i A') }}
                </div>

                <div style="font-weight:600;">
                    {{ $event->title }}
                </div>

                <div style="font-size:12px; color:#666;">
                    {{ $event->venue ?? 'Bhopal' }}
                </div>

            </div>
        </a>
        @endforeach

    </div>

</div>
@endif

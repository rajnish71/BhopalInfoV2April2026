@extends(theme_view('layouts.app'))

@section('content')

    {{-- ALERT --}}
    @if(isset($alert) && $alert)
        <div style="background:#B71C1C; color:#fff; padding:10px; margin-bottom:20px; text-align:center;">
            <strong>CRITICAL ALERT:</strong> {{ $alert->title }}
        </div>
    @endif

    {{-- HERO --}}
    <x-home.hero :heroPrimary="$heroPrimary" />

    <x-home.hero-secondary :posts="$heroSecondary" />

    {{-- LIVE STRIP --}}
    <x-home.live-strip :headline="$heroPrimary->title ?? null" />

    {{-- FILTERS --}}
    <x-home.filters :filters="['Events','Government','Traffic','Culture']" />

    {{-- MAIN CONTENT --}}
    <x-home.latest-updates
        :news="$news"
        :events="$upcomingEvents"
    />

@endsection

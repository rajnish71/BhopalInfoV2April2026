@extends(theme_view('layouts.app'))

@section('title', 'News & Updates | Bhopal Info')

@section('content')

<div style="padding: 40px 0 20px;">
    <p style="color:#B71C1C; font-weight:900; text-transform:uppercase; font-size:10px; letter-spacing:.08em; margin-bottom:8px;">Civic Infrastructure // Bhopal</p>
    <h1 style="font-size:32px; font-weight:900; text-transform:uppercase; letter-spacing:-.01em; margin:0 0 8px;">News & Updates</h1>
    <p style="font-size:13px; color:#888; margin:0;">Verified civic updates, alerts and service information — filtered by area.</p>
</div>

{{-- FILTERS --}}
<form method="GET" action="/news" style="margin-bottom:24px; padding:16px 0; border-top:1px solid #eee; border-bottom:1px solid #eee;">
    <div style="display:flex; flex-wrap:wrap; align-items:center; gap:12px;">
        <span style="color:#B71C1C; font-weight:900; text-transform:uppercase; font-size:10px;">Filter:</span>

        <select name="area" onchange="this.form.submit()" style="border:1px solid #E5E7EB; padding:5px 10px; font-size:11px; font-weight:700; text-transform:uppercase; background:#fff; color:#111; cursor:pointer;">
            <option value="">All Areas</option>
            @foreach($areas as $area)
                <option value="{{ $area->id }}" {{ request('area') == $area->id ? 'selected' : '' }}>{{ $area->name }}</option>
            @endforeach
        </select>

        <select name="category" onchange="this.form.submit()" style="border:1px solid #E5E7EB; padding:5px 10px; font-size:11px; font-weight:700; text-transform:uppercase; background:#fff; color:#111; cursor:pointer;">
            <option value="">All Categories</option>
            @foreach($categories as $cat)
                <option value="{{ $cat->id }}" {{ request('category') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
            @endforeach
        </select>

        <select name="urgency" onchange="this.form.submit()" style="border:1px solid #E5E7EB; padding:5px 10px; font-size:11px; font-weight:700; text-transform:uppercase; background:#fff; color:#111; cursor:pointer;">
            <option value="">All Urgency</option>
            <option value="critical" {{ request('urgency') === 'critical' ? 'selected' : '' }}>Critical</option>
            <option value="important" {{ request('urgency') === 'important' ? 'selected' : '' }}>Important</option>
            <option value="normal" {{ request('urgency') === 'normal' ? 'selected' : '' }}>Normal</option>
        </select>

        @if(request()->hasAny(['area', 'category', 'urgency']))
            <a href="/news" style="border:1px solid #E5E7EB; padding:4px 12px; font-size:11px; font-weight:700; text-transform:uppercase; background:#fff; color:#111; text-decoration:none;">Clear</a>
        @endif
    </div>
</form>

{{-- COUNT --}}
<div style="display:flex; justify-content:space-between; margin-bottom:20px;">
    <p style="font-size:10px; font-weight:900; text-transform:uppercase; color:#9CA3AF;">{{ $posts->total() }} updates</p>
    <p style="font-size:10px; font-weight:900; text-transform:uppercase; color:#9CA3AF;">Page {{ $posts->currentPage() }} of {{ $posts->lastPage() }}</p>
</div>

{{-- NEWS GRID --}}
@if($posts->count())
    <div style="display:grid; grid-template-columns:repeat(3,1fr); gap:16px; margin-bottom:40px;">
        @foreach($posts as $post)
            <div style="border:1px solid #E5E7EB; padding:20px; transition:border-color .15s;" onmouseover="this.style.borderColor='#B71C1C'" onmouseout="this.style.borderColor='#E5E7EB'">

                <div style="font-size:10px; font-weight:900; text-transform:uppercase; letter-spacing:.08em; color:#9CA3AF; display:flex; gap:6px; flex-wrap:wrap;">
                    <span style="color:#B71C1C;">{{ $post->area?->name ?? 'City Wide' }}</span>
                    <span>/</span>
                    <span>{{ $post->category?->name ?? 'General' }}</span>
                    <span>/</span>
                    <span>{{ $post->published_at?->diffForHumans() ?? '—' }}</span>
                </div>

                <h2 style="font-size:15px; font-weight:900; text-transform:uppercase; line-height:1.25; margin:8px 0 6px;">
                    <a href="/news/{{ $post->slug ?? $post->id }}" style="text-decoration:none; color:inherit;">{{ $post->title }}</a>
                </h2>

                @if($post->summary)
                    <p style="font-size:13px; color:#555; line-height:1.5; margin:0 0 12px;">{{ \Illuminate\Support\Str::limit($post->summary, 110) }}</p>
                @endif

                <div style="display:flex; align-items:center; justify-content:space-between; margin-top:12px;">
                    @php
                        $urgencyStyle = match($post->urgency_level) {
                            'critical' => 'background:#B71C1C; color:#fff;',
                            'important' => 'background:#111; color:#fff;',
                            default => 'background:#F3F4F6; color:#555;'
                        };
                    @endphp
                    <span style="{{ $urgencyStyle }} display:inline-block; padding:2px 8px; font-size:9px; font-weight:900; text-transform:uppercase;">{{ $post->urgency_level }}</span>
                    <a href="/news/{{ $post->slug ?? $post->id }}" style="font-size:10px; font-weight:900; text-transform:uppercase; color:#B71C1C; text-decoration:none; letter-spacing:.08em;">Read &rarr;</a>
                </div>

            </div>
        @endforeach
    </div>

    @if($posts->hasPages())
        <div style="display:flex; justify-content:center; gap:4px; flex-wrap:wrap; margin-bottom:40px;">
            <a href="{{ $posts->previousPageUrl() ?? '#' }}" style="display:inline-block; padding:6px 14px; border:1px solid #E5E7EB; font-size:11px; font-weight:700; text-transform:uppercase; text-decoration:none; color:#111; {{ $posts->onFirstPage() ? 'opacity:.35; pointer-events:none;' : '' }}">&larr; Prev</a>
            @foreach($posts->getUrlRange(1, $posts->lastPage()) as $page => $url)
                <a href="{{ $url }}" style="display:inline-block; padding:6px 14px; border:1px solid #E5E7EB; font-size:11px; font-weight:700; text-transform:uppercase; text-decoration:none; {{ $page === $posts->currentPage() ? 'background:#B71C1C; color:#fff; border-color:#B71C1C;' : 'color:#111;' }}">{{ $page }}</a>
            @endforeach
        </div>
    @endif

@else
    <div style="padding:60px 20px; text-align:center; border:1px dashed #E5E7EB; margin-bottom:40px;">
        <p style="color:#B71C1C; font-weight:900; text-transform:uppercase; font-size:10px; margin-bottom:8px;">No Results</p>
        <p style="font-size:13px; color:#888;">No published updates match your filters.</p>
        @if(request()->hasAny(['area', 'category', 'urgency']))
            <a href="/news" style="display:inline-block; margin-top:16px; border:1px solid #E5E7EB; padding:4px 12px; font-size:11px; font-weight:700; text-transform:uppercase; text-decoration:none; color:#111;">Clear Filters</a>
        @endif
    </div>
@endif

@endsection

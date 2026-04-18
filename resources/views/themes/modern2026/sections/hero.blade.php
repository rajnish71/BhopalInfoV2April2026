@php
$heroPrimary = \App\Models\NewsPost::where('publish_status', 'published')
    ->whereNotNull('published_at')
    ->orderByDesc('priority')
    ->orderByDesc('is_alert')
    ->orderByDesc('published_at')
    ->first();
@endphp

@if($heroPrimary)
<a href="/news/{{ $heroPrimary->id }}" style="display:block; margin:20px 0;">
    <div style="position:relative; border-radius:12px; overflow:hidden;">

        <img 
            src="{{ $heroPrimary->featured_image && file_exists(public_path('storage/'.$heroPrimary->featured_image)) ? asset('storage/'.$heroPrimary->featured_image) : 'https://via.placeholder.com/800x400' }}"
            style="width:100%; height:420px; object-fit:cover; display:block;">

        <div style="position:absolute; inset:0; background:linear-gradient(to top, rgba(0,0,0,0.9), transparent);"></div>

        <div style="position:absolute; bottom:20px; left:20px; color:#fff;">
            <h2 style="font-size:26px; font-weight:900;">
                {{ $heroPrimary->title }}
            </h2>

            <p style="font-size:12px;">
                {{ optional($heroPrimary->published_at)->diffForHumans() }}
            </p>
        </div>

    </div>
</a>
@endif

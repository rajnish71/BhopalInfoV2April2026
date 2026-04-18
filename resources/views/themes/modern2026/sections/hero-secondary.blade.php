@php
$heroSecondary = \App\Models\NewsPost::where('publish_status', 'published')
    ->whereNotNull('published_at')
    ->orderByDesc('priority')
    ->orderByDesc('is_alert')
    ->orderByDesc('published_at')
    ->skip(1)
    ->take(3)
    ->get();
@endphp

@if($heroSecondary && count($heroSecondary))
<div style="display:grid; grid-template-columns:repeat(3,1fr); gap:16px; margin-bottom:24px;">

    @foreach($heroSecondary as $post)
    <a href="/news/{{ $post->id }}">
        <div style="position:relative; border-radius:12px; overflow:hidden;">

            <img 
                src="{{ $post->featured_image && file_exists(public_path('storage/'.$post->featured_image)) ? asset('storage/'.$post->featured_image) : 'https://via.placeholder.com/800x400' }}"
                style="width:100%; height:200px; object-fit:cover; display:block;">

            <div style="position:absolute; inset:0; background:linear-gradient(to top, rgba(0,0,0,0.8), transparent);"></div>

            <div style="position:absolute; bottom:8px; left:8px; right:8px; color:#fff; font-size:12px; font-weight:600;">
                {{ \Illuminate\Support\Str::limit($post->title, 60) }}
            </div>

        </div>
    </a>
    @endforeach

</div>
@endif

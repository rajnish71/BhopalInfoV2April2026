@if($heroPrimary)
<a href="/news/{{ $heroPrimary->id }}" style="display:block; margin-top:20px; margin-bottom:16px; text-decoration:none;">
    <div style="position:relative; border-radius:12px; overflow:hidden;">

        {{-- HERO IMAGE --}}
        <img 
            src="{{ $heroPrimary->featured_image && file_exists(public_path('storage/'.$heroPrimary->featured_image)) ? asset('storage/'.$heroPrimary->featured_image) : 'https://via.placeholder.com/800x400' }}"
            style="width:100%; height:420px; object-fit:cover; display:block;"
        >

        {{-- OVERLAY --}}
        <div style="position:absolute; inset:0; background:linear-gradient(to top, rgba(0,0,0,0.9), rgba(0,0,0,0.2));"></div>

        {{-- TEXT --}}
        <div style="position:absolute; bottom:24px; left:24px; color:white; z-index:10;">
            <h2 style="font-size:28px; font-weight:bold; margin:0;">
                {{ $heroPrimary->title ?? 'City Updates in Bhopal' }}
            </h2>

            <p style="font-size:13px; margin-top:6px; opacity:0.85;">
                Bhopal • {{ optional($heroPrimary->published_at)->diffForHumans() }}
            </p>
        </div>

    </div>
</a>
@endif

@if($posts && count($posts))
<div style="display:grid; grid-template-columns:repeat(3,1fr); gap:16px; margin-bottom:24px;">

    @foreach($posts as $post)
    <a href="/news/{{ $post->id }}" style="display:block; text-decoration:none;">
        <div style="position:relative; border-radius:12px; overflow:hidden;">

            {{-- IMAGE --}}
            <img 
                src="{{ $post->featured_image && file_exists(public_path('storage/'.$post->featured_image)) ? asset('storage/'.$post->featured_image) : 'https://via.placeholder.com/800x400' }}"
                style="width:100%; height:200px; object-fit:cover; display:block;"
            >

            {{-- OVERLAY --}}
            <div style="position:absolute; inset:0; background:linear-gradient(to top, rgba(0,0,0,0.8), transparent);"></div>

            {{-- TITLE --}}
            <div style="position:absolute; bottom:8px; left:8px; right:8px; color:white; font-size:12px; font-weight:600;">
                {{ \Illuminate\Support\Str::limit($post->title, 60) }}
            </div>

        </div>
    </a>
    @endforeach

</div>
@endif

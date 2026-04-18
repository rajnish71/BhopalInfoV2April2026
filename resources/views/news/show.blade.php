<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $post->seo_title }} | Bhopal Info</title>
    <meta name="description" content="{{ $post->summary }}">
    <link rel="canonical" href="{{ url()->current() }}">
    {{-- <script type="application/ld+json">{!! $post->toJsonLd() !!}</script> --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body { font-family: 'Inter', sans-serif; background: #FFF; color: #111; line-height: 1.6; }
        .bg-brand { background-color: #B71C1C; }
        .max-w-prose { max-width: 65ch; margin: 0 auto; }
        .content-block-label { color: #B71C1C; font-weight: 900; text-transform: uppercase; font-size: 11px; margin-top: 2rem; border-b: 2px solid #EEE; display: block; }
    </style>
</head>
<body class="antialiased">
    <header class="py-6 border-b border-gray-100">
        <div class="max-w-4xl mx-auto px-6">
            <a href="/" class="text-xl font-black uppercase tracking-tighter">Bhopal<span class="text-brand">Info</span></a>
        </div>
    </header>

    <article class="py-12 px-6">
        <div class="max-w-4xl mx-auto">
            <div class="mb-8">
                <div class="flex items-center space-x-2 text-[10px] font-black uppercase text-gray-400 mb-4">
                    <span>{{ $post->category?->name }}</span>
                    <span>/</span>
                    <span class="text-brand">{{ $post->area?->name ?: 'CITY WIDE' }}</span>
                </div>
                <h1 class="text-4xl font-black uppercase tracking-tight leading-tight mb-6">{{ $post->title }}</h1>
                <p class="text-lg font-bold italic text-gray-500 border-l-4 border-gray-200 pl-6">{{ $post->summary }}</p>
            </div>

    <div class="mb-12 aspect-video bg-gray-100">
        <img src="{{ $post->featured_image && file_exists(public_path('storage/'.$post->featured_image)) ? asset('storage/'.$post->featured_image) : asset('images/hero.jpg') }}" class="w-full h-full object-cover" onerror="this.onerror=null;this.src='{{ asset('images/hero.jpg') }}';">
    </div>
            <div class="grid grid-cols-12 gap-12">
                <div class="col-span-8">
                    @foreach($post->content_blocks as $label => $text)
                        <span class="content-block-label">{{ $label }}</span>
                        <div class="mt-4 text-gray-800">{{ $text }}</div>
                    @endforeach
                </div>
                <div class="col-span-4">
                    <div class="bg-gray-50 p-6 border border-gray-100 sticky top-8">
                        <section class="mb-8">
                            <h4 class="text-[10px] font-black uppercase text-gray-400 mb-2">VERIFIED SOURCE</h4>
                            <p class="text-sm font-bold">{{ $post->source?->name }}</p>
                        </section>
                        <section class="mb-8">
                            <h4 class="text-[10px] font-black uppercase text-gray-400 mb-2">PUBLISHED ON</h4>
                            <p class="text-sm font-bold">{{ $post->published_at?->format('d M Y, H:i') }}</p>
                        </section>
                        <section>
                            <h4 class="text-[10px] font-black uppercase text-gray-400 mb-2">URGENCY</h4>
                            <span class="px-2 py-0.5 text-[10px] font-black uppercase {{ $post->urgency_level === 'critical' ? 'bg-red-600 text-white' : 'bg-gray-200 text-gray-600' }}">
                                {{ $post->urgency_level }}
                            </span>
                        </section>
                    </div>
                </div>
            </div>
        </div>
    </article>

    <footer class="bg-black text-white py-12 mt-20 text-center">
        <p class="text-[10px] font-black uppercase text-gray-500">Official Civic Infrastructure // Bhopal Info</p>
    </footer>
</body>
</html>

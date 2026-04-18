<body class="font-sans antialiased">

    <div class="min-h-screen bg-gray-100">

{{-- MAIN HEADER --}}
@include('themes.modern2026.partials.header') 
    
        {{-- OPTIONAL PAGE HEADER --}}
        @isset($header)
            <header class="bg-white shadow">
                <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                    {{ $header }}
                </div>
            </header>
        @endisset

        {{-- PAGE CONTENT --}}
        <main>
            {{ $slot }}
        </main>

    </div>

</body>

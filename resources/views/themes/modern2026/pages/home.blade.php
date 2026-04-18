@extends(theme_view('layouts.app'))

@section('content')

    @php
        $sections = theme_sections('home');
    @endphp

    @foreach($sections as $section)

        @includeIf(
            'themes.' . active_theme() . '.sections.' . $section,
            [
                'heroPrimary' => $heroPrimary,
                'heroSecondary' => $heroSecondary,
                'news' => $news,
                'upcomingEvents' => $upcomingEvents,
            ]
        )

    @endforeach

@endsection

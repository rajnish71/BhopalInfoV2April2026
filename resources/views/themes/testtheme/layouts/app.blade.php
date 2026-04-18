<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>@yield('title', 'Bhopal Info')</title>

    <link rel="icon" type="image/png" href="/favicon.png?v=1">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;700;900&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        body {
            font-family: 'Inter', sans-serif;
            background: #FAFAFA;
            font-size: 14px;
            color: #111;
        }
    </style>
</head>

<body class="antialiased">

<div style="max-width:1100px; margin:0 auto; padding:0 20px;">

    {{-- HEADER --}}
    @include('themes.modern2026.partials.header')

    {{-- PAGE CONTENT --}}
    @yield('content')

    {{-- FOOTER --}}
    @include('themes.modern2026.partials.footer')

</div>

</body>
</html>

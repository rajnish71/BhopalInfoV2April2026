@php use Illuminate\Support\Str; @endphp

<!DOCTYPE html>
<html>
<head>
    <title>{{ $event->title }} | Bhopal Info</title>
    <meta name="description" content="{{ Str::limit($event->description,150) }}">
</head>
<body style="font-family: Arial; max-width:800px; margin:auto;">

<a href="/events">← Back to Events</a>

<h1>{{ $event->title }}</h1>

<p><strong>Date:</strong> {{ $event->start_datetime }}</p>
<p><strong>Venue:</strong> {{ $event->venue }}</p>

<hr>

<p>{{ $event->description }}</p>

</body>
</html>

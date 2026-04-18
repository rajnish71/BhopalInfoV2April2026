<!DOCTYPE html>
<html>
<head>
    <title>Bhopal Events</title>

    <style>
        body {
            font-family: Arial;
            background: #f5f6fa;
            margin: 0;
            padding: 20px;
        }

        h1 {
            text-align: center;
            margin-bottom: 30px;
        }

        .grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 20px;
        }

        .card {
            background: white;
            border-radius: 12px;
            padding: 16px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.05);
            transition: transform 0.2s ease;
        }

        .card:hover {
            transform: translateY(-5px);
        }

        .title {
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 10px;
        }

        .meta {
            font-size: 14px;
            color: #555;
            margin-bottom: 6px;
        }

        .btn {
            display: inline-block;
            margin-top: 10px;
            padding: 8px 12px;
            background: #2563eb;
            color: white;
            border-radius: 6px;
            text-decoration: none;
            font-size: 14px;
        }
    </style>
</head>
<body>

<h1>Events in Bhopal</h1>

<div class="grid">

@forelse($events as $event)
    <div class="card">
        <div class="title">{{ $event->title }}</div>

        <div class="meta">📍 {{ $event->venue }}</div>
        <div class="meta">📅 {{ $event->start_datetime }}</div>

        <a class="btn" href="/events/{{ $event->slug }}">
            View Details
        </a>
    </div>
@empty
    <p>No events found.</p>
@endforelse

</div>

</body>
</html>

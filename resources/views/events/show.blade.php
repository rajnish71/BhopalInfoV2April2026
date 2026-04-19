@extends(theme_view('layouts.app'))

@section('title', $event->title . ' | Bhopal Info')

@section('content')

<div style="padding: 40px 0 20px;">
    <a href="/events" style="font-size:10px; font-weight:900; text-transform:uppercase; color:#B71C1C; text-decoration:none; letter-spacing:.08em;">&larr; Back to all events</a>
</div>

<article style="background:white; border:1px solid #eee; padding:40px; margin-bottom:60px;">
    <div style="display:flex; flex-direction:column; gap:20px;">
        
        <div>
            <p style="color:#B71C1C; font-weight:900; text-transform:uppercase; font-size:10px; letter-spacing:.08em; margin-bottom:8px;">Event Information</p>
            <h1 style="font-size:36px; font-weight:900; text-transform:uppercase; letter-spacing:-.01em; margin:0; line-height:1.1;">{{ $event->title }}</h1>
        </div>

        <div style="display:grid; grid-template-columns: 1fr 1fr; gap:30px; padding:25px 0; border-top:1px solid #eee; border-bottom:1px solid #eee;">
            <div>
                <p style="font-size:10px; font-weight:900; text-transform:uppercase; color:#9CA3AF; margin-bottom:5px;">Date & Time</p>
                <p style="font-size:16px; font-weight:700; margin:0;">{{ \Carbon\Carbon::parse($event->start_datetime)->format('l, d F Y') }}</p>
                <p style="font-size:14px; color:#555; margin:5px 0 0;">{{ \Carbon\Carbon::parse($event->start_datetime)->format('H:i') }} onwards</p>
            </div>
            <div>
                <p style="font-size:10px; font-weight:900; text-transform:uppercase; color:#9CA3AF; margin-bottom:5px;">Venue</p>
                <p style="font-size:16px; font-weight:700; margin:0;">{{ $event->venue }}</p>
                <p style="font-size:14px; color:#555; margin:5px 0 0;">Bhopal, MP</p>
            </div>
        </div>

        <div style="font-size:16px; line-height:1.8; color:#333; max-width:800px;">
            {!! nl2br(e($event->description)) !!}
        </div>

    </div>
</article>

@endsection

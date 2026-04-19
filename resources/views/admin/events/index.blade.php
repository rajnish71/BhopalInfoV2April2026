<x-admin-layout>
<div style="padding:24px;">

    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:24px;">
        <h1 style="font-size:20px; font-weight:700; margin:0;">Events Management</h1>
        <span style="font-size:12px; color:#888;">{{ $events->total() }} total events</span>
    </div>

    @if(session('success'))
        <div style="background:#EAF3DE; border:1px solid #8FB339; color:#27500A; padding:10px 16px; margin-bottom:16px; font-size:13px; font-weight:600;">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div style="background:#FCEBEB; border:1px solid #B71C1C; color:#791F1F; padding:10px 16px; margin-bottom:16px; font-size:13px; font-weight:600;">
            {{ session('error') }}
        </div>
    @endif

    <table style="width:100%; border-collapse:collapse; font-size:13px;">
        <thead>
            <tr style="background:#F9FAFB; border-bottom:2px solid #E5E7EB;">
                <th style="padding:10px 12px; text-align:left; font-weight:700; text-transform:uppercase; font-size:11px; color:#6B7280;">Title</th>
                <th style="padding:10px 12px; text-align:left; font-weight:700; text-transform:uppercase; font-size:11px; color:#6B7280;">Category</th>
                <th style="padding:10px 12px; text-align:left; font-weight:700; text-transform:uppercase; font-size:11px; color:#6B7280;">Start Date</th>
                <th style="padding:10px 12px; text-align:left; font-weight:700; text-transform:uppercase; font-size:11px; color:#6B7280;">Verification</th>
                <th style="padding:10px 12px; text-align:left; font-weight:700; text-transform:uppercase; font-size:11px; color:#6B7280;">Status</th>
                <th style="padding:10px 12px; text-align:left; font-weight:700; text-transform:uppercase; font-size:11px; color:#6B7280;">Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($events as $event)
            <tr style="border-bottom:1px solid #F3F4F6;">
                <td style="padding:12px;">
                    <div style="font-weight:600;">{{ $event->title }}</div>
                    <div style="font-size:11px; color:#9CA3AF;">{{ $event->venue }}</div>
                </td>
                <td style="padding:12px; color:#6B7280;">{{ $event->category?->name ?? '—' }}</td>
                <td style="padding:12px; color:#6B7280; white-space:nowrap;">
                    {{ $event->start_datetime ? \Carbon\Carbon::parse($event->start_datetime)->format('d M Y') : '—' }}
                </td>
                <td style="padding:12px;">
                    @php
                        $vStyle = match($event->verification_status) {
                            'verified'  => 'background:#EAF3DE; color:#27500A;',
                            'rejected'  => 'background:#FCEBEB; color:#791F1F;',
                            default     => 'background:#F3F4F6; color:#6B7280;'
                        };
                    @endphp
                    <span style="{{ $vStyle }} padding:2px 8px; font-size:10px; font-weight:700; text-transform:uppercase;">
                        {{ $event->verification_status }}
                    </span>
                </td>
                <td style="padding:12px;">
                    @php
                        $pStyle = match($event->publish_status) {
                            'published' => 'background:#EAF3DE; color:#27500A;',
                            'review'    => 'background:#FAEEDA; color:#633806;',
                            'archived'  => 'background:#F3F4F6; color:#6B7280;',
                            default     => 'background:#F3F4F6; color:#6B7280;'
                        };
                    @endphp
                    <span style="{{ $pStyle }} padding:2px 8px; font-size:10px; font-weight:700; text-transform:uppercase;">
                        {{ $event->publish_status }}
                    </span>
                </td>
                <td style="padding:12px;">
                    <div style="display:flex; gap:6px; flex-wrap:wrap;">
                        @if($event->verification_status === 'pending')
                            <form method="POST" action="{{ route('admin.events.verify', $event) }}" style="margin:0;">
                                @csrf
                                <button type="submit" style="background:#1D4ED8; color:#fff; border:none; padding:4px 10px; font-size:11px; font-weight:700; text-transform:uppercase; cursor:pointer;">
                                    Verify
                                </button>
                            </form>
                        @endif
                        @if($event->verification_status === 'verified' && $event->publish_status === 'review')
                            <form method="POST" action="{{ route('admin.events.publish', $event) }}" style="margin:0;">
                                @csrf
                                <button type="submit" style="background:#B71C1C; color:#fff; border:none; padding:4px 10px; font-size:11px; font-weight:700; text-transform:uppercase; cursor:pointer;">
                                    Publish
                                </button>
                            </form>
                        @endif
                        @if($event->publish_status === 'published')
                            <form method="POST" action="{{ route('admin.events.archive', $event) }}" style="margin:0;">
                                @csrf
                                <button type="submit" style="background:#111; color:#fff; border:none; padding:4px 10px; font-size:11px; font-weight:700; text-transform:uppercase; cursor:pointer;">
                                    Archive
                                </button>
                            </form>
                        @endif
                        @if(!in_array($event->publish_status, ['published', 'archived']) && $event->verification_status === 'pending')
                            <span style="font-size:11px; color:#9CA3AF; padding:4px 0;">Awaiting verification</span>
                        @endif
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="6" style="padding:40px; text-align:center; color:#9CA3AF; font-size:13px;">
                    No events found.
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <div style="margin-top:20px;">
        {{ $events->links() }}
    </div>

</div>
</x-admin-layout>

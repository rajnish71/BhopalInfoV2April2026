<div style="display:flex; gap:10px; flex-wrap:wrap; margin-bottom:30px;">

    @foreach($filters as $filter)
        <span style="padding:6px 12px; border:1px solid #ddd; border-radius:20px; font-size:12px;">
            {{ $filter }}
        </span>
    @endforeach

</div>

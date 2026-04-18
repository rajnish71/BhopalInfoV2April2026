@extends('admin.layouts.app')

@section('content')

<h2 class="text-xl font-bold mb-6">Homepage Sections</h2>

<ul id="section-list" class="space-y-3">

@foreach($sections as $section)
<li class="bg-white border p-4 rounded flex justify-between items-center cursor-move"
    data-name="{{ $section->section }}">

    <div>
        <strong>{{ $section->section }}</strong>
    </div>

    <div>
        <input type="checkbox" class="toggle"
            {{ $section->is_enabled ? 'checked' : '' }}>
    </div>

</li>
@endforeach

</ul>

<button onclick="saveSections()" class="mt-6 bg-black text-white px-4 py-2">
    Save Changes
</button>

<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>

<script>
new Sortable(document.getElementById('section-list'), {
    animation: 150
});

function saveSections() {

    let sections = [];

    document.querySelectorAll('#section-list li').forEach((el, index) => {
        sections.push({
            name: el.dataset.name,
            enabled: el.querySelector('.toggle').checked ? 1 : 0
        });
    });

    fetch("{{ route('admin.theme.sections.update') }}", {
        method: "POST",
        headers: {
            "Content-Type": "application/json",
            "X-CSRF-TOKEN": "{{ csrf_token() }}"
        },
        body: JSON.stringify({ sections })
    })
    .then(res => res.json())
    .then(() => {
        alert("Saved!");
        location.reload();
    });
}
</script>

@endsection

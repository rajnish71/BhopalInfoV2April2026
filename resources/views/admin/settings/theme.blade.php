<x-admin-layout>

    <h2 class="text-xl font-bold mb-6">Theme Settings</h2>

    @if(session('success'))
        <div class="mb-4 p-3 bg-green-100 text-green-700 rounded">
            {{ session('success') }}
        </div>
    @endif

    <!-- ========================= -->
    <!-- THEME SWITCHER -->
    <!-- ========================= -->
    <form method="POST" action="{{ url('/admin/theme') }}">
        @csrf

        <div class="grid grid-cols-2 gap-6">

            @foreach($themes as $theme)
            <label class="border p-4 rounded-lg cursor-pointer transition
                {{ $activeTheme === $theme['key'] ? 'border-green-500 ring-2 ring-green-200' : 'border-gray-300' }}">

                <input type="radio" name="theme" value="{{ $theme['key'] }}"
                       {{ $activeTheme === $theme['key'] ? 'checked' : '' }}
                       class="mb-3">

                @if($theme['preview'])
                    <img src="{{ asset($theme['preview']) }}" class="rounded mb-3 w-full">
                @endif

                <div class="font-bold text-sm">{{ $theme['name'] }}</div>
                <div class="text-xs text-gray-500">{{ $theme['description'] }}</div>

                <div class="text-[10px] text-gray-400 mt-1">
                    v{{ $theme['version'] ?? '1.0' }}
                </div>

            </label>
            @endforeach

        </div>

        <button class="mt-6 px-5 py-2 bg-black text-white text-sm font-bold rounded">
            Save Theme
        </button>
    </form>

    <!-- ========================= -->
    <!-- SECTION LAYOUT -->
    <!-- ========================= -->
    <hr class="my-10">

    <h2 class="text-lg font-bold mb-4">Homepage Layout</h2>

    <ul id="sectionsList" class="space-y-3">

        @foreach(\DB::table('theme_sections')->where('page', 'home')->orderBy('position')->get() as $section)
        <li class="p-4 border rounded flex justify-between items-center bg-white cursor-move"
            data-name="{{ $section->section }}">

            <div class="flex items-center gap-3">
                <span>☰</span>
                <span class="font-medium">{{ ucfirst($section->section) }}</span>
            </div>

            <label>
                <input type="checkbox"
                       class="toggle"
                       {{ $section->is_enabled ? 'checked' : '' }}>
                Enabled
            </label>

        </li>
        @endforeach

    </ul>

    <button onclick="saveSections()" class="mt-4 px-4 py-2 bg-black text-white rounded">
        Save Layout
    </button>

    <!-- ========================= -->
    <!-- SECTION SETTINGS -->
    <!-- ========================= -->
    <hr class="my-10">

    <h2 class="text-lg font-bold mb-4">Section Settings</h2>

    <form method="POST" action="/admin/theme/settings">
        @csrf

        <div class="space-y-6">

            <!-- HERO -->
            <div class="p-4 border rounded">
                <h3 class="font-semibold mb-2">Hero Section</h3>

                <label>Category ID:</label>
<label>Category:</label>

<select name="settings[hero][category]" class="border p-2 w-full">
    <option value="">-- Auto / All --</option>

    @foreach($categories as $id => $name)
        <option value="{{ $id }}"
            {{ section_setting('hero','category') == $id ? 'selected' : '' }}>
            {{ $name }}
        </option>
    @endforeach
</select>                

                       value="{{ section_setting('hero','category') }}"
                       class="border p-2 w-full">

                <label class="mt-2 block">Limit:</label>
                <input type="number" name="settings[hero][limit]"
                       value="{{ section_setting('hero','limit',1) }}"
                       class="border p-2 w-full">
            </div>

            <!-- LATEST -->
            <div class="p-4 border rounded">
                <h3 class="font-semibold mb-2">Latest Updates</h3>

                <label>Limit:</label>
                <input type="number" name="settings[latest-updates][limit]"
                       value="{{ section_setting('latest-updates','limit',5) }}"
                       class="border p-2 w-full">
            </div>

        </div>

        <button class="mt-4 px-4 py-2 bg-black text-white rounded">
            Save Settings
        </button>
    </form>

    <!-- ========================= -->
    <!-- SCRIPTS -->
    <!-- ========================= -->

    <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>

    <script>
        new Sortable(document.getElementById('sectionsList'), {
            animation: 150
        });

        function saveSections() {

            let sections = [];

            document.querySelectorAll('#sectionsList li').forEach((el, index) => {
                sections.push({
                    name: el.dataset.name,
                    enabled: el.querySelector('.toggle').checked ? 1 : 0
                });
            });

            fetch('/admin/theme/sections', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ sections })
            })
            .then(res => res.json())
            .then(() => {
                alert('Layout saved successfully');
                location.reload();
            });
        }
    </script>

</x-admin-layout>

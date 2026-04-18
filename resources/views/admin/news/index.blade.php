<x-admin-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight border-l-4 border-[#B71C1C] pl-4">NEWS ENGINE</h2>
            <a href="{{ route('admin.news.create') }}" class="bg-[#B71C1C] text-white px-6 py-2 text-sm font-bold uppercase tracking-wider hover:bg-red-800 transition">Add New Entry</a>
        </div>
    </x-slot>
    <div class="mb-6 flex space-x-2">
        <a href="{{ route('admin.news.index') }}" class="px-4 py-2 text-xs font-bold border {{ !request('status') ? 'bg-gray-800 text-white' : 'bg-white text-gray-600 border-gray-200' }}">ALL</a>
        <a href="{{ route('admin.news.index', ['status' => 'published']) }}" class="px-4 py-2 text-xs font-bold border {{ request('status') == 'published' ? 'bg-gray-800 text-white' : 'bg-white text-gray-600 border-gray-200' }}">PUBLISHED</a>
        <a href="{{ route('admin.news.index', ['urgency' => 'critical']) }}" class="px-4 py-2 text-xs font-bold border {{ request('urgency') == 'critical' ? 'bg-red-600 text-white border-red-600' : 'bg-white text-red-600 border-red-200' }}">CRITICAL</a>
    </div>
    @if(session('success')) <div class="mb-4 p-4 bg-green-100 text-green-700 border-l-4 border-green-500 font-bold text-xs uppercase">{{ session('success') }}</div> @endif
    <div class="overflow-x-auto bg-white border border-gray-200">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr><th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">Title / Issue</th><th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">Classification</th><th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">Status</th><th class="px-6 py-3 text-right text-xs font-bold text-gray-500 uppercase">Actions</th></tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @foreach($posts as $post)
                <tr><td class="px-6 py-4"><div class="text-sm font-bold text-gray-900">{{ $post->title }}</div></td><td class="px-6 py-4"><div class="text-xs font-medium text-gray-700">{{ $post->category?->name }}</div></td><td class="px-6 py-4 text-xs font-black uppercase text-gray-400">P: {{ $post->publish_status }}</td><td class="px-6 py-4 text-right"><a href="{{ route('admin.news.edit', $post) }}" class="text-[#B71C1C] hover:text-red-900 uppercase font-black text-xs">Edit</a></td></tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="mt-4">{{ $posts->links() }}</div>
</x-admin-layout>
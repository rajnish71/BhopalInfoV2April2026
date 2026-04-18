<x-admin-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Media Library</h2>
    </x-slot>
    <div class="mb-8 bg-white p-6 border border-gray-200">
        <form action="{{ route('admin.media.store') }}" method="POST" enctype="multipart/form-data" class="flex items-end space-x-4">
            @csrf
            <div class="flex-1">
                <label class="block text-sm font-medium text-gray-700">Upload Image</label>
                <input type="file" name="image" required class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:border-0 file:text-sm file:font-semibold file:bg-gray-100 file:text-gray-700 hover:file:bg-gray-200">
            </div>
            <div class="flex-1">
                <label class="block text-sm font-medium text-gray-700">Alt Text</label>
                <input type="text" name="alt_text" class="mt-1 block w-full border-gray-300 focus:border-brand focus:ring-brand">
            </div>
            <button type="submit" class="bg-brand text-white px-6 py-2 hover:bg-red-800 transition">Upload</button>
        </form>
    </div>
    @if(session('success')) <div class="mb-4 p-4 bg-green-100 text-green-700">{{ session('success') }}</div> @endif
    <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-4">
        @foreach($media as $item)
        <div class="relative group aspect-square bg-gray-100 border border-gray-200 overflow-hidden">
            <img src="{{ asset('storage/' . $item->file_path) }}" alt="{{ $item->alt_text }}" class="w-full h-full object-cover">
            <div class="absolute inset-0 bg-black bg-opacity-50 opacity-0 group-hover:opacity-100 transition flex flex-col justify-center items-center text-white p-2">
                <p class="text-xs text-center border-b border-white/20 pb-1 mb-1 w-full truncate">{{ $item->alt_text ?: 'No alt text' }}</p>
                <form action="{{ route('admin.media.destroy', $item) }}" method="POST" onsubmit="return confirm('Delete this image?')">
                    @csrf @method('DELETE')
                    <button type="submit" class="text-xs bg-red-600 px-2 py-1 hover:bg-red-700">Delete</button>
                </form>
            </div>
        </div>
        @endforeach
    </div>
    <div class="mt-6">{{ $media->links() }}</div>
</x-admin-layout>
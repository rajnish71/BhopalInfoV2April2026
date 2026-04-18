<x-admin-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ isset($category) ? 'Edit Category' : 'Create Category' }}
        </h2>
    </x-slot>

    <div class="max-w-2xl">
        <form action="{{ isset($category) ? route('admin.categories.update', $category) : route('admin.categories.store') }}" method="POST" class="space-y-6">
            @csrf
            @if(isset($category)) @method('PUT') @endif

            <div>
                <label class="block text-sm font-medium text-gray-700">Category Name</label>
                <input type="text" name="name" value="{{ old('name', $category->name ?? '') }}" required class="mt-1 block w-full border-gray-300 focus:border-brand focus:ring-brand">
                @error('name')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">Type</label>
                <select name="type" required class="mt-1 block w-full border-gray-300 focus:border-brand focus:ring-brand">
                    @foreach(['news', 'event', 'contest', 'mixed'] as $type)
                        <option value="{{ $type }}" {{ old('type', $category->type ?? '') === $type ? 'selected' : '' }}>{{ ucfirst($type) }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">Status</label>
                <select name="status" required class="mt-1 block w-full border-gray-300 focus:border-brand focus:ring-brand">
                    <option value="active" {{ old('status', $category->status ?? '') === 'active' ? 'selected' : '' }}>Active</option>
                    <option value="inactive" {{ old('status', $category->status ?? '') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                </select>
            </div>

            <div class="pt-4 flex items-center space-x-4">
                <button type="submit" class="bg-brand text-white px-8 py-2 hover:bg-red-800 transition">Save</button>
                <a href="{{ route('admin.categories.index') }}" class="text-gray-600 hover:text-gray-900">Cancel</a>
            </div>
        </form>
    </div>
</x-admin-layout>
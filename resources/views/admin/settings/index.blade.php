<x-admin-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Site Settings') }}
        </h2>
    </x-slot>

    <div class="max-w-4xl">
        @if(session('success'))
            <div class="mb-4 p-4 bg-green-100 border-l-4 border-green-500 text-green-700">
                {{ session('success') }}
            </div>
        @endif

        <form action="{{ route('admin.settings.update') }}" method="POST" class="space-y-6">
            @csrf
            
            <div class="grid grid-cols-1 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Site Name</label>
                    <input type="text" name="site_name" value="{{ $settings['site_name'] ?? '' }}" class="mt-1 block w-full border-gray-300 focus:border-brand focus:ring-brand">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Site Tagline</label>
                    <input type="text" name="site_tagline" value="{{ $settings['site_tagline'] ?? '' }}" class="mt-1 block w-full border-gray-300 focus:border-brand focus:ring-brand">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Contact Email</label>
                    <input type="email" name="contact_email" value="{{ $settings['contact_email'] ?? '' }}" class="mt-1 block w-full border-gray-300 focus:border-brand focus:ring-brand">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Social Links (JSON)</label>
                    <textarea name="social_links" rows="3" class="mt-1 block w-full border-gray-300 focus:border-brand focus:ring-brand">{{ $settings['social_links'] ?? '' }}</textarea>
                    <p class="mt-1 text-xs text-gray-500">Format: {"facebook": "url", "twitter": "url"}</p>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Logo Path</label>
                        <input type="text" name="logo_path" value="{{ $settings['logo_path'] ?? '' }}" class="mt-1 block w-full border-gray-300 focus:border-brand focus:ring-brand">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Favicon Path</label>
                        <input type="text" name="favicon_path" value="{{ $settings['favicon_path'] ?? '' }}" class="mt-1 block w-full border-gray-300 focus:border-brand focus:ring-brand">
                    </div>
                </div>
            </div>

            <div class="pt-4">
                <button type="submit" class="inline-flex justify-center py-2 px-10 border border-transparent text-sm font-medium text-white bg-brand hover:bg-red-800 transition">
                    Save Changes
                </button>
            </div>
        </form>
    </div>
</x-admin-layout>
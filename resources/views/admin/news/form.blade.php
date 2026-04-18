<x-admin-layout>
    <x-slot name="header"><h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ isset($post) ? 'EDIT: ' . $post->title : 'NEW CIVIC NOTICE' }}</h2></x-slot>
    <form action="{{ isset($post) ? route('admin.news.update', $post) : route('admin.news.store') }}" method="POST" class="grid grid-cols-12 gap-8">
        @csrf @if(isset($post)) @method('PUT') @endif
        <div class="col-span-8 space-y-8">
            <div class="bg-gray-50 p-6 border border-gray-200">
                <div class="space-y-4">
                    <div><label class="block text-xs font-bold text-gray-700 uppercase mb-1">Title</label><input type="text" name="title" value="{{ old('title', $post->title ?? '') }}" required class="w-full border-gray-300 focus:border-[#B71C1C] focus:ring-0 text-sm font-bold"></div>
                    <div><label class="block text-xs font-bold text-gray-700 uppercase mb-1">Summary</label><textarea name="summary" rows="2" required class="w-full border-gray-300 focus:border-[#B71C1C] focus:ring-0 text-sm italic">{{ old('summary', $post->summary ?? '') }}</textarea></div>
                    <div class="grid grid-cols-2 gap-4">
                        <div><label class="block text-xs font-bold text-gray-700 uppercase mb-1">Type</label><select name="news_type" class="w-full border-gray-300 focus:border-[#B71C1C] text-xs">@foreach(['Routine Civic Update', 'Emergency Alert', 'Service Guide'] as $t)<option value="{{ $t }}" {{ old('news_type', $post->news_type ?? '') == $t ? 'selected' : '' }}>{{ $t }}</option>@endforeach</select></div>
                        <div><label class="block text-xs font-bold text-gray-700 uppercase mb-1">Image URL</label><input type="text" name="featured_image" value="{{ old('featured_image', $post->featured_image ?? '') }}" class="w-full border-gray-300 focus:border-[#B71C1C] text-xs"></div>
                    </div>
                </div>
            </div>
            <div class="bg-white p-6 border border-gray-200">
                <h3 class="text-xs font-black uppercase text-[#B71C1C] mb-4 border-b pb-2">STRUCTURED CONTENT</h3>
                <div class="space-y-4">
                    @foreach($content_blocks as $bn => $bc)
                    <div><label class="block text-[10px] font-black text-gray-400 uppercase mb-1">{{ $bn }}</label><textarea name="content_blocks[{{ $bn }}]" rows="2" required class="w-full border-gray-200 bg-gray-50 focus:bg-white focus:border-[#B71C1C] text-sm">{{ old("content_blocks.{$bn}", $bc) }}</textarea></div>
                    @endforeach
                </div>
            </div>
        </div>
        <div class="col-span-4 space-y-8">
            <div class="bg-gray-50 p-6 border border-gray-200 space-y-4">
                <div><label class="block text-xs font-bold uppercase mb-1">City</label><select name="city_id" class="w-full border-gray-300 text-xs">@foreach($cities as $c)<option value="{{ $c->id }}">{{ $c->name }}</option>@endforeach</select></div>
                <div><label class="block text-xs font-bold uppercase mb-1">Category</label><select name="category_id" class="w-full border-gray-300 text-xs">@foreach($categories as $cat)<option value="{{ $cat->id }}">{{ $cat->name }}</option>@endforeach</select></div>
                <div><label class="block text-xs font-bold uppercase mb-1">Source</label><select name="source_id" class="w-full border-gray-300 text-xs">@foreach($sources as $s)<option value="{{ $s->id }}">{{ $s->name }}</option>@endforeach</select></div>
            </div>
            <div class="bg-[#B71C1C] text-white p-6 space-y-4 border-0">
                <div><label class="block text-xs font-bold uppercase mb-1">Urgency</label><select name="urgency_level" id="urgency_level" class="w-full border-white/20 bg-white/10 text-xs font-bold text-white"><option value="low" class="text-gray-900">LOW</option><option value="critical" class="text-gray-900" {{ old('urgency_level', $post->urgency_level ?? '') == 'critical' ? 'selected' : '' }}>CRITICAL</option></select></div>
                <div><label class="block text-xs font-bold uppercase mb-1">Workflow Status</label><select name="publish_status" id="publish_status" class="w-full border-white/20 bg-white/10 text-xs font-bold text-white uppercase"><option value="draft" class="text-gray-900">Draft</option><option value="published" class="text-gray-900" {{ old('publish_status', $post->publish_status ?? '') == 'published' ? 'selected' : '' }}>Published</option></select></div>
                <div><label class="block text-xs font-bold uppercase mb-1">Verification</label><select name="verification_status" class="w-full border-white/20 bg-white/10 text-xs font-bold text-white"><option value="unverified" class="text-gray-900">UNVERIFIED</option><option value="verified" class="text-gray-900" {{ old('verification_status', $post->verification_status ?? '') == 'verified' ? 'selected' : '' }}>VERIFIED</option></select></div>
{{-- PRIORITY --}}
<div>
    <label class="block text-xs font-bold uppercase mb-1">Priority (0–10)</label>
    <input type="number" name="priority"
        value="{{ old('priority', $post->priority ?? 0) }}"
        min="0" max="10"
        class="w-full border-white/20 bg-white/10 text-xs font-bold text-white">
</div>

{{-- ALERT --}}
<div>
    <label class="flex items-center gap-2 text-xs font-bold uppercase">
        <input type="checkbox" name="is_alert" value="1"
            {{ old('is_alert', $post->is_alert ?? 0) ? 'checked' : '' }}>
        🚨 Mark as Alert
    </label>
</div>

{{-- SUBMIT --}}
<button type="submit" id="submit_btn"
    class="w-full bg-white text-[#B71C1C] py-3 text-sm font-black uppercase tracking-widest hover:bg-gray-100 transition">
    Update Record
</button>
            </div>
        </div>
    </form>
    <script>document.getElementById('submit_btn').onclick = function(e) { if (document.getElementById('urgency_level').value === 'critical' && document.getElementById('publish_status').value === 'published') { if (!confirm('CRITICAL ALERT: Broadcast to citizens?')) e.preventDefault(); } };</script>
</x-admin-layout>

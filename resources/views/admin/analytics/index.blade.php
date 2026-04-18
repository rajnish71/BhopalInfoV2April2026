<x-admin-layout>
    <x-slot name="header"><h2 class="font-semibold text-xl text-gray-800 leading-tight">City Intelligence Dashboard</h2></x-slot>
    <div class="grid grid-cols-4 gap-6 mb-12">
        <div class="bg-white p-6 border-l-4 border-[#B71C1C]">
            <p class="text-[10px] font-black text-gray-400 uppercase">Critical Alerts</p>
            <p class="text-3xl font-black">{{ $criticalAlerts }}</p>
        </div>
        <div class="bg-white p-6 border-l-4 border-gray-800">
            <p class="text-[10px] font-black text-gray-400 uppercase">Verified Records</p>
            <p class="text-3xl font-black">{{ $totalVerified }}</p>
        </div>
    </div>
    <div class="grid grid-cols-2 gap-12">
        <div class="bg-white p-8 border border-gray-100">
            <h3 class="text-xs font-black uppercase mb-6 border-b pb-2">Posts by Pillar Type</h3>
            @foreach($postsByPillar as $pillar)
            <div class="mb-4">
                <div class="flex justify-between text-xs font-bold mb-1"><span>{{ $pillar->name }}</span><span>{{ $pillar->news_posts_count }}</span></div>
                <div class="w-full bg-gray-100 h-2"><div class="bg-[#B71C1C] h-2" style="width: {{ min(100, $pillar->news_posts_count * 5) }}%"></div></div>
            </div>
            @endforeach
        </div>
        <div class="bg-white p-8 border border-gray-100">
            <h3 class="text-xs font-black uppercase mb-6 border-b pb-2">Area Heatmap</h3>
            <table class="w-full text-xs">
                <thead><tr class="text-gray-400 font-bold uppercase"><th class="text-left pb-4">Area</th><th class="text-right pb-4">Activity</th></tr></thead>
                <tbody>
                    @foreach($areaHeatmap as $area)
                    <tr><td class="py-2 font-bold">{{ $area->name }}</td><td class="py-2 text-right font-black">{{ $area->news_posts_count }}</td></tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</x-admin-layout>
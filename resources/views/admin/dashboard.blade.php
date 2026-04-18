<x-admin-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Admin Dashboard</h2>
    </x-slot>

    <!-- Metric Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-5 gap-6 mb-8">
        <div class="bg-white p-6 border-l-4 border-[#B71C1C] shadow-sm">
            <p class="text-xs font-bold text-gray-500 uppercase">Total Users</p>
            <p class="text-2xl font-semibold text-gray-900">{{ $stats['users'] }}</p>
        </div>
        <div class="bg-white p-6 border-l-4 border-gray-800 shadow-sm">
            <p class="text-xs font-bold text-gray-500 uppercase">Categories</p>
            <p class="text-2xl font-semibold text-gray-900">{{ $stats['categories'] }}</p>
        </div>
        <div class="bg-white p-6 border-l-4 border-[#B71C1C] shadow-sm">
            <p class="text-xs font-bold text-gray-500 uppercase">Media Files</p>
            <p class="text-2xl font-semibold text-gray-900">{{ $stats['media'] }}</p>
        </div>
        <div class="bg-white p-6 border-l-4 border-gray-800 shadow-sm">
            <p class="text-xs font-bold text-gray-500 uppercase">Audit Logs</p>
            <p class="text-2xl font-semibold text-gray-900">{{ $stats['audit_logs'] }}</p>
        </div>
        <div class="bg-white p-6 border-l-4 border-[#B71C1C] shadow-sm">
            <p class="text-xs font-bold text-gray-500 uppercase">Active Roles</p>
            <p class="text-2xl font-semibold text-gray-900">{{ $stats['roles'] }}</p>
        </div>
    </div>

    <!-- Latest Activity Table -->
    <div class="bg-white border border-gray-200">
        <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
            <h3 class="text-lg font-medium text-gray-900">Latest Activity</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">User</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Action</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Module</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">IP Address</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($latest_activity as $log)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $log->user_name ?: 'System' }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $log->action }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $log->module }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $log->ip_address }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm text-gray-500">{{ $log->created_at }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-10 text-center text-sm text-gray-500 italic">No activity logs found.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4 border-t border-gray-200">
            <a href="#" class="text-sm font-medium text-[#B71C1C] hover:text-red-800">View All Audit Logs →</a>
        </div>
    </div>
</x-admin-layout>
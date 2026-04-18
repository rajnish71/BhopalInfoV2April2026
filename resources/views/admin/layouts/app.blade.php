<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Admin - {{ $site['site_name'] ?? config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="font-sans antialiased bg-gray-100 text-gray-900">

<div class="flex h-screen overflow-hidden">

    <!-- SIDEBAR -->
    <aside class="w-64 bg-gray-900 text-white flex-shrink-0 flex flex-col border-r border-gray-800">

        <!-- LOGO -->
        <div class="p-6 text-xl font-bold border-b border-gray-800">
            {{ $site['site_name'] ?? "LOGO" }}
        </div>

        <!-- NAV -->
        <nav class="flex-1 overflow-y-auto py-4">
            <ul class="space-y-1">

                <!-- DASHBOARD -->
                <li>
                    <a href="{{ route('admin.dashboard') }}"
                       class="block px-6 py-3 {{ request()->routeIs('admin.dashboard') ? 'bg-[#B71C1C] text-white' : 'hover:bg-gray-800 text-gray-300' }} font-bold text-xs uppercase tracking-widest">
                        Dashboard
                    </a>
                </li>

                <!-- NEWS -->
                <li class="px-6 py-4 text-[10px] font-black text-gray-500 uppercase tracking-widest">
                    News Publishing
                </li>

                <li>
                    <a href="{{ route('admin.news.index') }}"
                       class="block px-6 py-2 {{ request()->routeIs('admin.news.index') ? 'text-white' : 'text-gray-400 hover:text-white' }} text-xs font-bold uppercase transition pl-8">
                        All News
                    </a>
                </li>

                <li>
                    <a href="{{ route('admin.news.create') }}"
                       class="block px-6 py-2 {{ request()->routeIs('admin.news.create') ? 'text-white' : 'text-gray-400 hover:text-white' }} text-xs font-bold uppercase transition pl-8">
                        Add New
                    </a>
                </li>

                <!-- CLASSIFICATION -->
                <li class="px-6 py-4 text-[10px] font-black text-gray-500 uppercase tracking-widest">
                    Classification
                </li>

                <li>
                    <a href="{{ route('admin.categories.index') }}"
                       class="block px-6 py-2 {{ request()->routeIs('admin.categories.*') ? 'text-white' : 'text-gray-400 hover:text-white' }} text-xs font-bold uppercase transition pl-8">
                        Categories
                    </a>
                </li>

                <!-- SYSTEMS -->
                <li class="px-6 py-4 text-[10px] font-black text-gray-500 uppercase tracking-widest">
                    Systems
                </li>

                <li>
                    <a href="{{ route('admin.settings.index') }}"
                       class="block px-6 py-2 {{ request()->routeIs('admin.settings.*') ? 'text-white' : 'text-gray-400 hover:text-white' }} text-xs font-bold uppercase transition pl-8">
                        Audit Logs
                    </a>
                </li>

                <!-- ✅ NEW: THEME SETTINGS -->
                <li>
                    <a href="{{ url('/admin/theme') }}"
                       class="block px-6 py-2 {{ request()->is('admin/theme') ? 'text-white' : 'text-gray-400 hover:text-white' }} text-xs font-bold uppercase transition pl-8">
                        Theme Settings
                    </a>
                </li>

            </ul>
        </nav>

        <!-- FOOTER -->
        <div class="p-4 border-t border-gray-800">
            <p class="text-xs text-gray-500">v1.0.0</p>
        </div>

    </aside>

    <!-- MAIN -->
    <div class="flex-1 flex flex-col overflow-hidden">

        <!-- TOP BAR -->
        <header class="h-16 bg-white border-b border-gray-200 flex items-center justify-between px-8 flex-shrink-0">
            <div class="text-sm font-medium uppercase tracking-wider text-gray-500">
                Administration Panel
            </div>

            <div class="flex items-center space-x-4">
                <span class="text-sm text-gray-600">Admin User</span>
                <div class="h-8 w-8 bg-[#B71C1C] rounded-full"></div>
                
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="bg-[#B71C1C] text-white px-4 py-2 text-[10px] font-bold uppercase tracking-widest hover:opacity-90 transition-opacity">
                        Logout
                    </button>
                </form>
            </div>
        </header>

        <!-- CONTENT -->
        <main class="flex-1 overflow-y-auto p-8">

            @isset($header)
                <div class="mb-8">
                    {{ $header }}
                </div>
            @endisset

            <div class="bg-white p-8 border border-gray-200">
                {{ $slot }}
            </div>

        </main>

        <!-- FOOTER -->
        <footer class="p-6 bg-white border-t border-gray-200 text-sm text-gray-500">
            &copy; {{ date('Y') }} {{ $site['site_name'] ?? "Bhopal Admin Core" }}. All rights reserved.
        </footer>

    </div>

</div>

</body>
</html>

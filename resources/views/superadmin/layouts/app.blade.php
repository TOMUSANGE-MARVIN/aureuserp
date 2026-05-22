<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin Console') — AureusERP</title>
    <script>
        (function() {
            const t = localStorage.getItem('theme');
            if (t === 'dark' || (!t && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
                document.documentElement.classList.add('dark');
            }
        })();
    </script>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        * { box-sizing: border-box; }
        body { font-family: 'Inter', sans-serif; }
        [x-cloak] { display: none !important; }

        /* Sidebar */
        .sa-sidebar { width: 240px; transition: width .25s ease; flex-shrink: 0; }

        /* Nav items */
        .sa-nav-item { transition: background .15s, color .15s; }
        .sa-nav-item.active { background: rgba(99,102,241,0.12); color: #6366f1; }
        .dark .sa-nav-item.active { background: rgba(99,102,241,0.18); color: #a5b4fc; }
        .sa-nav-item:not(.active):hover { background: rgba(0,0,0,0.04); }
        .dark .sa-nav-item:not(.active):hover { background: rgba(255,255,255,0.05); }

        /* Stat card */
        .sa-stat { transition: box-shadow .2s, transform .2s; }
        .sa-stat:hover { transform: translateY(-2px); box-shadow: 0 8px 25px rgba(99,102,241,0.12); }

        /* Scrollbar */
        ::-webkit-scrollbar { width: 4px; height: 4px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: #d1d5db; border-radius: 4px; }
        .dark ::-webkit-scrollbar-thumb { background: #374151; }

        .sa-group-label {
            font-size: 10px; font-weight: 600; letter-spacing: .09em;
            text-transform: uppercase; padding: 14px 14px 4px; color: #9ca3af;
        }
        .dark .sa-group-label { color: #4b5563; }
    </style>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    fontFamily: { sans: ['Inter', 'sans-serif'] },
                    colors: {
                        indigo: {
                            50:'#eef2ff', 100:'#e0e7ff', 200:'#c7d2fe',
                            400:'#818cf8', 500:'#6366f1', 600:'#4f46e5',
                            700:'#4338ca', 800:'#3730a3', 900:'#312e81'
                        }
                    }
                }
            }
        }
    </script>
</head>
<body class="h-full bg-gray-50 dark:bg-[#0d0d14] text-gray-900 dark:text-white flex overflow-hidden">

@php
$currentPath = request()->path();
$navItems = [
    ['label' => 'Dashboard',      'href' => '/superadmin',                  'match' => ['superadmin'],
     'icon'  => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>'],
    ['label' => 'Organizations',  'href' => '/superadmin/organizations',     'match' => ['superadmin/organizations'],
     'icon'  => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>'],
    ['label' => 'Subscriptions',  'href' => '/superadmin/subscriptions',     'match' => ['superadmin/subscriptions'],
     'icon'  => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>'],
    ['label' => 'Plans',          'href' => '/superadmin/plans',             'match' => ['superadmin/plans'],
     'icon'  => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 7h3m-3 4h3m0-4h3m-3 4h3"/>'],
    ['label' => 'Users',          'href' => '/superadmin/users',             'match' => ['superadmin/users'],
     'icon'  => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>'],
    ['label' => 'Analytics',      'href' => '/superadmin/analytics',         'match' => ['superadmin/analytics'],
     'icon'  => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>'],
];
@endphp

<!-- ═══════════════ SIDEBAR ═══════════════ -->
<aside class="sa-sidebar bg-white dark:bg-[#0f0f1a] border-r border-gray-100 dark:border-white/5 flex flex-col h-screen overflow-hidden">
<!-- Brand -->
<div class="flex items-center justify-center px-4 py-4 border-b border-gray-100 dark:border-white/5 flex-shrink-0">
    <img src="{{ asset('images/aura.png') }}" alt="Logo" class="h-10 w-auto max-w-full object-contain">
</div>

    <!-- Nav -->
    <nav class="flex-1 overflow-y-auto py-3 px-2">
        @foreach($navItems as $item)
            @php $active = in_array($currentPath, $item['match']) || str_starts_with($currentPath, $item['match'][0].'/'); @endphp
            <a href="{{ $item['href'] }}"
               class="sa-nav-item flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium mb-0.5 {{ $active ? 'active' : 'text-gray-600 dark:text-gray-400' }}">
                <svg class="flex-shrink-0 {{ $active ? 'text-indigo-500 dark:text-indigo-400' : 'text-gray-400 dark:text-gray-500' }}"
                     style="width:18px;height:18px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    {!! $item['icon'] !!}
                </svg>
                <span>{{ $item['label'] }}</span>
            </a>
        @endforeach
    </nav>

    <!-- Footer: user info + logout -->
    <div class="border-t border-gray-100 dark:border-white/5 px-3 py-3 flex-shrink-0 space-y-2">
        <div class="flex items-center gap-3 px-2 py-1">
            <div class="w-7 h-7 rounded-full bg-indigo-500 flex items-center justify-center text-white text-xs font-bold flex-shrink-0">
                {{ strtoupper(substr(Auth::guard('superadmin')->user()?->name ?? 'A', 0, 2)) }}
            </div>
            <div class="min-w-0 flex-1">
                <div class="text-xs font-semibold text-gray-800 dark:text-gray-200 truncate">{{ Auth::guard('superadmin')->user()?->name ?? 'Admin' }}</div>
                <div class="text-[10px] text-indigo-500 font-medium">Super Admin</div>
            </div>
        </div>
        <form method="POST" action="/superadmin/logout">
            @csrf
            <button type="submit"
                    class="w-full flex items-center gap-2 px-3 py-2 rounded-xl text-sm font-medium text-red-500 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20 transition-colors">
                <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                </svg>
                Sign Out
            </button>
        </form>
    </div>
</aside>

<!-- ═══════════════ MAIN ═══════════════ -->
<div class="flex-1 flex flex-col min-w-0 overflow-hidden">
    <!-- Topbar -->
    <header class="h-14 bg-white dark:bg-[#0f0f1a] border-b border-gray-100 dark:border-white/5 flex items-center gap-4 px-5 flex-shrink-0 z-20">
        <div class="flex items-center gap-1.5 text-sm text-gray-400 dark:text-gray-500">
            <span>Admin Console</span>
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            <span class="text-gray-700 dark:text-gray-300 font-medium">@yield('breadcrumb', 'Dashboard')</span>
        </div>

        <div class="ml-auto flex items-center gap-2">
            @yield('topbar_actions')

            <!-- Dark mode -->
            <button id="themeToggle" class="p-2 rounded-lg text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 hover:bg-gray-100 dark:hover:bg-white/5 transition-colors">
                <svg class="w-5 h-5 dark:hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/>
                </svg>
                <svg class="w-5 h-5 hidden dark:block" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364-6.364l-.707.707M6.343 17.657l-.707.707M17.657 17.657l-.707-.707M6.343 6.343l-.707-.707M12 8a4 4 0 100 8 4 4 0 000-8z"/>
                </svg>
            </button>

        </div>
    </header>

    <!-- Content -->
    <main class="flex-1 overflow-y-auto px-5 py-5 space-y-5">
        @yield('content')
    </main>
</div>

<script>
document.getElementById('themeToggle').addEventListener('click', () => {
    const dark = !document.documentElement.classList.contains('dark');
    document.documentElement.classList.toggle('dark', dark);
    localStorage.setItem('theme', dark ? 'dark' : 'light');
    setTimeout(() => location.reload(), 80);
});
</script>
@yield('scripts')
</body>
</html>

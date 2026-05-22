<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'AureusERP') — AureusERP</title>
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
    @yield('head_scripts')
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    fontFamily: { sans: ['Inter', 'sans-serif'] },
                    colors: {
                        brand: {
                            50:'#f5f3ff', 100:'#ede9fe', 200:'#ddd6fe', 300:'#c4b5fd',
                            400:'#a78bfa', 500:'#8b5cf6', 600:'#7c3aed', 700:'#6d28d9',
                            800:'#5b21b6', 900:'#4c1d95'
                        }
                    }
                }
            }
        }
    </script>
    <style>
        * { box-sizing: border-box; }
        body { font-family: 'Inter', sans-serif; }
        [x-cloak] { display: none !important; }

        /* Sidebar */
        .sidebar { width: 248px; transition: width .25s ease; flex-shrink: 0; }
        .sidebar.collapsed { width: 64px; }
        .sidebar.collapsed .nav-label,
        .sidebar.collapsed .nav-group-label,
        .sidebar.collapsed .sidebar-brand-text { display: none; }
        .sidebar.collapsed .nav-item { justify-content: center; padding-left: 0; padding-right: 0; }
        .sidebar.collapsed .sidebar-brand { justify-content: center; }

        /* Nav item */
        .nav-item { transition: background .15s, color .15s; }
        .nav-item.active { background: rgba(124,58,237,0.1); color: #7c3aed; }
        .dark .nav-item.active { background: rgba(139,92,246,0.15); color: #a78bfa; }
        .nav-item:not(.active):hover { background: rgba(0,0,0,0.04); }
        .dark .nav-item:not(.active):hover { background: rgba(255,255,255,0.05); }

        /* Stat card hover */
        .stat-card { transition: box-shadow .2s, transform .2s; }
        .stat-card:hover { transform: translateY(-2px); box-shadow: 0 8px 25px rgba(124,58,237,0.12); }
        .dark .stat-card:hover { box-shadow: 0 8px 25px rgba(0,0,0,0.35); }

        /* Thin scrollbar */
        ::-webkit-scrollbar { width: 4px; height: 4px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: #d1d5db; border-radius: 4px; }
        .dark ::-webkit-scrollbar-thumb { background: #374151; }

        /* Nav group label */
        .nav-group-label {
            font-size: 10px; font-weight: 600; letter-spacing: .09em;
            text-transform: uppercase; padding: 14px 14px 4px;
            color: #9ca3af;
        }
        .dark .nav-group-label { color: #4b5563; }

        /* Demo badge */
        .demo-badge {
            display: inline-flex; align-items: center; gap: 4px;
            background: #fef3c7; color: #92400e;
            font-size: 11px; font-weight: 600;
            padding: 3px 10px; border-radius: 100px;
            border: 1px solid #fde68a;
        }
        .dark .demo-badge { background: rgba(251,191,36,.12); color: #fcd34d; border-color: rgba(251,191,36,.25); }

        /* Notification dot */
        .notif-dot {
            position: absolute; top: 4px; right: 4px;
            width: 7px; height: 7px; border-radius: 50%;
            background: #ef4444; border: 2px solid white;
        }
        .dark .notif-dot { border-color: #111827; }

        /* User avatar */
        .user-avatar { background: linear-gradient(135deg, #7c3aed, #a78bfa); }

        /* Mobile sidebar */
        .sidebar-overlay { display: none; position: fixed; inset: 0; background: rgba(0,0,0,.5); z-index: 30; }
        @media (max-width: 768px) {
            .sidebar { position: fixed; top: 0; left: -248px; height: 100vh; z-index: 40; transition: left .25s ease; }
            .sidebar.mobile-open { left: 0; }
            .sidebar-overlay.active { display: block; }
            .main-content { margin-left: 0 !important; }
        }

        /* Chart cards */
        .chart-card { min-height: 280px; }

        /* Badge pill */
        .badge { display: inline-flex; align-items: center; gap: 4px; padding: 2px 10px; border-radius: 100px; font-size: 11px; font-weight: 600; }
        .badge-up { background: #ecfdf5; color: #059669; }
        .dark .badge-up { background: rgba(5,150,105,.15); color: #34d399; }
        .badge-down { background: #fef2f2; color: #dc2626; }
        .dark .badge-down { background: rgba(220,38,38,.15); color: #f87171; }
        .badge-neutral { background: #f1f5f9; color: #64748b; }
        .dark .badge-neutral { background: rgba(100,116,139,.15); color: #94a3b8; }
    </style>
    @yield('styles')
</head>
<body class="h-full bg-gray-50 dark:bg-[#0d0d12] text-gray-900 dark:text-gray-100 flex overflow-hidden">

<!-- ══════════════════ SIDEBAR ══════════════════ -->
@php
$currentPath = request()->path();

// Helper: check if any child path matches current path
$anyChildActive = fn($children) => collect($children)->contains(fn($c) => str_starts_with($currentPath, $c['match']));

// Determine user's allowed modules (Admin sees all; users with no assignments see all)
$isAdmin = Auth::check() && (
    Auth::user()->is_superadmin ||
    \Illuminate\Support\Facades\DB::table('model_has_roles')
        ->join('roles', 'roles.id', '=', 'model_has_roles.role_id')
        ->where('model_has_roles.model_id', Auth::id())
        ->where('model_has_roles.model_type', 'App\\Models\\User')
        ->where('roles.name', 'Admin')
        ->exists()
);
$userModules = ($isAdmin || !Auth::check()) ? null : \Illuminate\Support\Facades\DB::table('user_module_access')
    ->where('user_id', Auth::id())
    ->pluck('module')
    ->toArray();
// If user has no explicit module assignments, show everything (unset = full access)
$hasRestrictions = $userModules !== null && count($userModules) > 0;
$canSee = fn(string $moduleKey) => !$hasRestrictions || in_array($moduleKey, $userModules);

$navItems = [
    ['group' => null, 'items' => [
        ['label' => 'Overview', 'href' => '/app/dashboard', 'match' => 'app/dashboard', 'module' => 'dashboard',
         'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>'],
    ]],

    ['group' => 'Sales', 'items' => [
        ['label' => 'Contacts', 'href' => '/app/contacts', 'match' => 'app/contacts', 'module' => 'contacts',
         'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0"/>'],
        ['label' => 'Sales', 'href' => '/app/sales', 'match' => 'app/sales', 'module' => 'sales',
         'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M16 8v8m-4-5v5m-4-2v2m-2 4h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>',
         'children' => [
            ['label' => 'Orders', 'href' => '/app/sales', 'match' => 'app/sales'],
            ['label' => 'New Order', 'href' => '/app/sales/create', 'match' => 'app/sales/create'],
         ]],
        ['label' => 'Purchases', 'href' => '/app/purchases', 'match' => 'app/purchases', 'module' => 'purchases',
         'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>',
         'children' => [
            ['label' => 'Purchase Orders', 'href' => '/app/purchases', 'match' => 'app/purchases'],
            ['label' => 'New PO', 'href' => '/app/purchases/create', 'match' => 'app/purchases/create'],
         ]],
    ]],

    ['group' => 'Operations', 'items' => [
        ['label' => 'Products', 'href' => '/app/products', 'match' => 'app/products', 'module' => 'products',
         'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>'],
        ['label' => 'Inventory', 'href' => '/app/inventory', 'match' => 'app/inventory', 'module' => 'inventory',
         'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/>'],
        ['label' => 'Manufacturing', 'href' => '/app/manufacturing', 'match' => 'app/manufacturing', 'module' => 'manufacturing',
         'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"/>',
         'children' => [
            ['label' => 'Mfg Orders', 'href' => '/app/manufacturing', 'match' => 'app/manufacturing'],
            ['label' => 'Work Orders', 'href' => '/app/manufacturing/work-orders', 'match' => 'app/manufacturing/work-orders'],
            ['label' => 'Bills of Materials', 'href' => '/app/manufacturing/bom', 'match' => 'app/manufacturing/bom'],
         ]],
    ]],

    ['group' => 'People', 'items' => [
        ['label' => 'Employees', 'href' => '/app/employees', 'match' => 'app/employees', 'module' => 'employees',
         'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>'],
        ['label' => 'Projects', 'href' => '/app/projects', 'match' => 'app/projects', 'module' => 'projects',
         'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/>'],
        ['label' => 'Recruitment', 'href' => '/app/recruitment', 'match' => 'app/recruitment', 'module' => 'recruitment',
         'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>'],
        ['label' => 'Time Off', 'href' => '/app/time-off', 'match' => 'app/time-off', 'module' => 'time_off',
         'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>'],
    ]],

    ['group' => 'Finance', 'items' => [
        ['label' => 'Accounting', 'href' => '/app/accounting', 'match' => 'app/accounting', 'module' => 'accounting',
         'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 7H6a2 2 0 00-2 2v9a2 2 0 002 2h9a2 2 0 002-2v-3M9 7V5a2 2 0 012-2h2a2 2 0 012 2v2M9 7h6"/>',
         'children' => [
            ['label' => 'Invoices', 'href' => '/app/accounting?type=out_invoice', 'match' => 'app/accounting'],
            ['label' => 'Bills', 'href' => '/app/accounting?type=in_invoice', 'match' => 'app/accounting'],
            ['label' => 'Journal Entries', 'href' => '/app/accounting?type=entry', 'match' => 'app/accounting'],
         ]],
        ['label' => 'Payroll', 'href' => '/app/payroll', 'match' => 'app/payroll', 'module' => 'payroll',
         'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>',
         'children' => [
            ['label' => 'Payslips', 'href' => '/app/payroll', 'match' => 'app/payroll'],
            ['label' => 'Salary Structures', 'href' => '/app/payroll/structures', 'match' => 'app/payroll/structures'],
         ]],
    ]],

    ['group' => 'Digital', 'items' => [
        ['label' => 'Website', 'href' => '/app/website', 'match' => 'app/website', 'module' => 'website',
         'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"/>',
         'children' => [
            ['label' => 'Pages', 'href' => '/app/website', 'match' => 'app/website'],
            ['label' => 'Blog Posts', 'href' => '/app/blog', 'match' => 'app/blog'],
         ]],
        ['label' => 'Helpdesk', 'href' => '/app/helpdesk', 'match' => 'app/helpdesk', 'module' => 'helpdesk',
         'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z"/>',
         'children' => [
            ['label' => 'Tickets', 'href' => '/app/helpdesk', 'match' => 'app/helpdesk'],
            ['label' => 'Teams', 'href' => '/app/helpdesk/teams', 'match' => 'app/helpdesk/teams'],
         ]],
    ]],

    ['group' => 'System', 'items' => [
        ['label' => 'Settings', 'href' => '/app/settings', 'match' => 'app/settings', 'module' => 'settings',
         'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>',
         'children' => [
            ['label' => 'General', 'href' => '/app/settings', 'match' => 'app/settings'],
            ['label' => 'Users', 'href' => '/app/settings/users', 'match' => 'app/settings/users'],
            ['label' => 'Roles & Permissions', 'href' => '/app/settings/roles', 'match' => 'app/settings/roles'],
            ['label' => 'Activity Types', 'href' => '/app/settings/activity-types', 'match' => 'app/settings/activity-types'],
            ['label' => 'Currencies', 'href' => '/app/settings/currencies', 'match' => 'app/settings/currencies'],
         ]],
    ]],

];
@endphp

<aside class="sidebar bg-white dark:bg-[#111118] border-r border-gray-100 dark:border-white/5 flex flex-col h-screen overflow-hidden" id="sidebar">
    <!-- Brand -->
    <div class="sidebar-brand flex items-center justify-center px-4 py-4 border-b border-gray-100 dark:border-white/5 flex-shrink-0">
        <img src="{{ asset('images/aura.png') }}" alt="Logo" class="h-10 w-auto max-w-full object-contain sidebar-brand-text">
    </div>

    <!-- Navigation -->
    <nav class="flex-1 overflow-y-auto py-3 px-2" x-data>
        @foreach($navItems as $group)
            @php
                // Filter items the user can see
                $visibleItems = array_filter($group['items'], fn($item) => $canSee($item['module'] ?? 'dashboard'));
            @endphp
            @if(count($visibleItems) === 0) @continue @endif
            @if($group['group'])
            <p class="nav-group-label">{{ $group['group'] }}</p>
            @endif
            @foreach($visibleItems as $item)
                @php
                    $isActive = str_starts_with($currentPath, $item['match']);
                    $hasChildren = !empty($item['children']);
                    $childActive = $hasChildren && $anyChildActive($item['children']);
                    $itemActive = $isActive || $childActive;
                @endphp

                @if($hasChildren)
                {{-- Collapsible dropdown item --}}
                <div x-data="{ open: {{ ($itemActive ? 'true' : 'false') }} }" class="space-y-0.5">
                    <button @click="open = !open"
                       class="w-full nav-item flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium cursor-pointer {{ $itemActive ? 'active' : 'text-gray-600 dark:text-gray-400' }}">
                        <svg class="flex-shrink-0 {{ $itemActive ? 'text-brand-600 dark:text-brand-400' : 'text-gray-400 dark:text-gray-500' }}"
                             style="width:18px;height:18px;"
                             fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            {!! $item['icon'] !!}
                        </svg>
                        <span class="nav-label flex-1 text-left">{{ $item['label'] }}</span>
                        <svg class="nav-label flex-shrink-0 w-3.5 h-3.5 transition-transform duration-200 {{ $itemActive ? 'text-brand-500' : 'text-gray-400' }}"
                             :class="open ? 'rotate-90' : ''"
                             fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                        </svg>
                    </button>
                    <div x-show="open" x-cloak x-transition:enter="transition ease-out duration-150"
                         x-transition:enter-start="opacity-0 -translate-y-1"
                         x-transition:enter-end="opacity-100 translate-y-0"
                         class="ml-4 pl-3 border-l-2 border-gray-100 dark:border-white/8 space-y-0.5">
                        @foreach($item['children'] as $child)
                            @php $childIsActive = str_starts_with($currentPath, $child['match']) && request()->fullUrl() === url($child['href']); @endphp
                            <a href="{{ $child['href'] }}"
                               class="flex items-center gap-2 px-3 py-2 rounded-lg text-sm cursor-pointer transition-colors
                                      {{ str_starts_with($currentPath, $child['match']) ? 'text-brand-600 dark:text-brand-400 font-medium bg-brand-50 dark:bg-brand-900/20' : 'text-gray-500 dark:text-gray-400 hover:text-gray-800 dark:hover:text-gray-200 hover:bg-gray-50 dark:hover:bg-white/5' }}">
                                <span class="w-1.5 h-1.5 rounded-full flex-shrink-0 {{ str_starts_with($currentPath, $child['match']) ? 'bg-brand-500' : 'bg-gray-300 dark:bg-gray-600' }}"></span>
                                {{ $child['label'] }}
                            </a>
                        @endforeach
                    </div>
                </div>
                @else
                {{-- Regular flat item --}}
                <a href="{{ $item['href'] }}"
                   class="nav-item flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium cursor-pointer {{ $itemActive ? 'active' : 'text-gray-600 dark:text-gray-400' }}">
                    <svg class="flex-shrink-0 {{ $itemActive ? 'text-brand-600 dark:text-brand-400' : 'text-gray-400 dark:text-gray-500' }}"
                         style="width:18px;height:18px;"
                         fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        {!! $item['icon'] !!}
                    </svg>
                    <span class="nav-label">{{ $item['label'] }}</span>
                </a>
                @endif
            @endforeach
        @endforeach
    </nav>

    <!-- User snippet -->
    <div class="border-t border-gray-100 dark:border-white/5 px-3 py-3 flex-shrink-0">
        <div class="flex items-center gap-3 px-2 py-1">
            <div class="user-avatar w-7 h-7 rounded-full flex items-center justify-center text-white text-xs font-bold flex-shrink-0">
                {{ strtoupper(substr(Auth::user()?->name ?? 'U', 0, 2)) }}
            </div>
            <div class="nav-label min-w-0">
                <div class="text-xs font-semibold text-gray-800 dark:text-gray-200 truncate">{{ Auth::user()?->name ?? 'User' }}</div>
                <div class="text-[10px] text-gray-400 truncate">{{ Auth::user()?->email ?? '' }}</div>
            </div>
        </div>
    </div>
</aside>

<!-- Mobile overlay -->
<div class="sidebar-overlay" id="sidebarOverlay"></div>

<!-- ══════════════════ MAIN CONTENT ══════════════════ -->
<div class="flex-1 flex flex-col min-w-0 overflow-hidden" id="mainContent">

    <!-- Topbar -->
    <header class="h-14 bg-white dark:bg-[#111118] border-b border-gray-100 dark:border-white/5 flex items-center gap-3 px-4 lg:px-5 flex-shrink-0 z-20">
        <button id="sidebarToggle" class="p-1.5 rounded-lg text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 hover:bg-gray-100 dark:hover:bg-white/5 transition-colors">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
            </svg>
        </button>

        <!-- Breadcrumb -->
        <div class="hidden md:flex items-center gap-1.5 text-sm text-gray-400 dark:text-gray-500">
            <img src="{{ asset('images/aura.png') }}" alt="Logo" class="h-5 w-auto object-contain opacity-70">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            <span class="text-gray-700 dark:text-gray-300 font-medium">@yield('breadcrumb', 'Dashboard')</span>
        </div>

        <!-- Search -->
        <div class="flex-1 max-w-xs hidden md:block ml-2">
            <div class="relative">
                <svg class="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
                <input type="text" placeholder="Search..." class="w-full pl-9 pr-4 py-1.5 text-sm bg-gray-50 dark:bg-white/5 border border-gray-200 dark:border-white/8 rounded-lg text-gray-700 dark:text-gray-300 placeholder-gray-400 focus:outline-none focus:border-brand-400 transition-colors">
            </div>
        </div>

        <div class="ml-auto flex items-center gap-1.5">
            @yield('topbar_actions')

            <!-- Notifications -->
            <div class="relative" x-data="notifDropdown()" x-init="init()">
                <button @click="toggle()"
                        class="relative p-2 rounded-lg text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 hover:bg-gray-100 dark:hover:bg-white/5 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                    </svg>
                    <!-- Unread count badge -->
                    <span x-show="unreadCount > 0" x-text="unreadCount > 99 ? '99+' : unreadCount"
                          class="absolute -top-0.5 -right-0.5 min-w-[16px] h-4 px-0.5 rounded-full bg-red-500 text-white text-[10px] font-bold flex items-center justify-center leading-none"
                          style="display:none;"></span>
                </button>

                <!-- Dropdown -->
                <div x-show="open" @click.outside="open = false" x-cloak
                     x-transition:enter="transition ease-out duration-150"
                     x-transition:enter-start="opacity-0 scale-95 translate-y-1"
                     x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                     x-transition:leave="transition ease-in duration-100"
                     x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                     x-transition:leave-end="opacity-0 scale-95 translate-y-1"
                     class="absolute right-0 mt-2 w-80 bg-white dark:bg-[#1a1a2e] border border-gray-100 dark:border-white/10 rounded-2xl shadow-xl z-50 overflow-hidden">

                    <!-- Header -->
                    <div class="flex items-center justify-between px-4 py-3 border-b border-gray-100 dark:border-white/5">
                        <span class="font-semibold text-sm text-gray-900 dark:text-white">Notifications
                            <span x-show="unreadCount > 0" x-text="'(' + unreadCount + ' new)'"
                                  class="text-brand-500 font-normal"></span>
                        </span>
                        <button x-show="unreadCount > 0" @click="markAllRead()"
                                class="text-xs text-brand-500 hover:text-brand-600 font-medium transition-colors">
                            Mark all read
                        </button>
                    </div>

                    <!-- Notification list -->
                    <div class="max-h-80 overflow-y-auto divide-y divide-gray-50 dark:divide-white/5">
                        <template x-if="loading">
                            <div class="px-4 py-6 text-center text-sm text-gray-400">
                                <svg class="w-5 h-5 animate-spin mx-auto mb-2 text-brand-400" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                                </svg>
                                Loading…
                            </div>
                        </template>

                        <template x-if="!loading && items.length === 0">
                            <div class="px-4 py-8 text-center">
                                <svg class="w-8 h-8 mx-auto text-gray-300 dark:text-gray-600 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                                </svg>
                                <p class="text-sm text-gray-400">All caught up!</p>
                            </div>
                        </template>

                        <template x-for="n in items" :key="n.id">
                            <div @click="handleClick(n)"
                                 class="flex items-start gap-3 px-4 py-3 cursor-pointer transition-colors"
                                 :class="n.is_read ? 'hover:bg-gray-50 dark:hover:bg-white/3' : 'bg-brand-50/50 dark:bg-violet-900/10 hover:bg-brand-50 dark:hover:bg-violet-900/20'">

                                <!-- Type icon -->
                                <div class="w-8 h-8 rounded-full flex items-center justify-center flex-shrink-0 mt-0.5"
                                     :class="{
                                         'bg-blue-100 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400': n.type === 'ticket',
                                         'bg-green-100 dark:bg-green-900/30 text-green-600 dark:text-green-400': n.type === 'payroll',
                                         'bg-purple-100 dark:bg-purple-900/30 text-purple-600 dark:text-purple-400': n.type === 'hr',
                                         'bg-red-100 dark:bg-red-900/30 text-red-600 dark:text-red-400': n.type === 'alert',
                                         'bg-gray-100 dark:bg-white/8 text-gray-500 dark:text-gray-400': !['ticket','payroll','hr','alert'].includes(n.type),
                                     }">
                                    <!-- Ticket icon -->
                                    <template x-if="n.type === 'ticket'">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"/></svg>
                                    </template>
                                    <!-- Payroll icon -->
                                    <template x-if="n.type === 'payroll'">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"/></svg>
                                    </template>
                                    <!-- HR icon -->
                                    <template x-if="n.type === 'hr'">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                    </template>
                                    <!-- Alert icon -->
                                    <template x-if="n.type === 'alert'">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                                    </template>
                                    <!-- Default bell icon -->
                                    <template x-if="!['ticket','payroll','hr','alert'].includes(n.type)">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                                    </template>
                                </div>

                                <!-- Text -->
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm text-gray-900 dark:text-white truncate"
                                       :class="n.is_read ? 'font-normal' : 'font-semibold'"
                                       x-text="n.title"></p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5 line-clamp-1" x-text="n.body" x-show="n.body"></p>
                                    <p class="text-xs text-gray-400 mt-0.5" x-text="n.time_ago"></p>
                                </div>

                                <!-- Unread dot -->
                                <span x-show="!n.is_read" class="w-2 h-2 rounded-full bg-brand-500 flex-shrink-0 mt-1.5"></span>
                            </div>
                        </template>
                    </div>

                    <!-- Footer -->
                    <div class="border-t border-gray-100 dark:border-white/5 px-4 py-2.5">
                        <a href="/app/notifications" @click="open = false"
                           class="block text-center text-xs font-medium text-brand-500 hover:text-brand-600 transition-colors">
                            View all notifications →
                        </a>
                    </div>
                </div>
            </div>

            <!-- Dark mode toggle -->
            <button id="themeToggle" class="p-2 rounded-lg text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 hover:bg-gray-100 dark:hover:bg-white/5 transition-colors" aria-label="Toggle dark mode">
                <svg class="w-5 h-5 dark:hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/>
                </svg>
                <svg class="w-5 h-5 hidden dark:block" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364-6.364l-.707.707M6.343 17.657l-.707.707M17.657 17.657l-.707-.707M6.343 6.343l-.707-.707M12 8a4 4 0 100 8 4 4 0 000-8z"/>
                </svg>
            </button>

            <!-- User menu -->
            <div class="relative" x-data="{ open: false }">
                <button @click="open = !open" class="flex items-center gap-2 rounded-xl px-2 py-1 hover:bg-gray-100 dark:hover:bg-white/5 transition-colors">
                    <div class="user-avatar w-8 h-8 rounded-full flex items-center justify-center text-white text-xs font-bold flex-shrink-0">
                        {{ strtoupper(substr(Auth::user()?->name ?? 'U', 0, 2)) }}
                    </div>
                    <span class="hidden md:block text-sm font-medium text-gray-700 dark:text-gray-300 max-w-28 truncate">{{ Auth::user()?->name ?? 'User' }}</span>
                    <svg class="w-3.5 h-3.5 text-gray-400 hidden md:block" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                </button>

                <div x-show="open" @click.outside="open = false" x-cloak
                     class="absolute right-0 mt-2 w-48 bg-white dark:bg-[#1a1a2e] border border-gray-100 dark:border-white/10 rounded-2xl shadow-lg overflow-hidden z-50">
                    <div class="px-4 py-3 border-b border-gray-100 dark:border-white/5">
                        <p class="text-sm font-semibold text-gray-900 dark:text-white truncate">{{ Auth::user()?->name ?? 'User' }}</p>
                        <p class="text-xs text-gray-400 truncate mt-0.5">{{ Auth::user()?->email ?? '' }}</p>
                    </div>
                    <form method="POST" action="/admin/logout">
                        @csrf
                        <button type="submit"
                                class="w-full flex items-center gap-2.5 px-4 py-3 text-sm text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20 transition-colors font-medium">
                            <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                            Sign Out
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </header>

    <!-- Page content -->
    <main class="flex-1 overflow-y-auto px-4 lg:px-6 py-5 space-y-5">
        @yield('content')
    </main>
</div>

<!-- ══════════════════ SHARED SCRIPTS ══════════════════ -->
<script>
// Theme toggle
document.getElementById('themeToggle').addEventListener('click', () => {
    const dark = !document.documentElement.classList.contains('dark');
    document.documentElement.classList.toggle('dark', dark);
    localStorage.setItem('theme', dark ? 'dark' : 'light');
    setTimeout(() => location.reload(), 80);
});

// Sidebar toggle
const sidebar = document.getElementById('sidebar');
const overlay = document.getElementById('sidebarOverlay');
document.getElementById('sidebarToggle').addEventListener('click', () => {
    if (window.innerWidth >= 769) {
        sidebar.classList.toggle('collapsed');
    } else {
        sidebar.classList.toggle('mobile-open');
        overlay.classList.toggle('active');
    }
});
overlay.addEventListener('click', () => {
    sidebar.classList.remove('mobile-open');
    overlay.classList.remove('active');
});
</script>
@yield('scripts')

<script>
// ════════════════════════════════════════════════
// Notifications Dropdown Alpine.js Component
// ════════════════════════════════════════════════
function notifDropdown() {
    return {
        open: false,
        loading: false,
        items: [],
        unreadCount: 0,
        pollTimer: null,

        init() {
            this.fetchUnread();
            // Poll every 45 seconds for new notifications
            this.pollTimer = setInterval(() => this.fetchUnread(), 45000);
        },

        toggle() {
            this.open = !this.open;
            if (this.open) {
                this.loading = true;
                this.fetchUnread(() => { this.loading = false; });
            }
        },

        fetchUnread(callback) {
            fetch('/app/notifications/unread', {
                headers: { 'Accept': 'application/json' }
            })
            .then(r => r.json())
            .then(data => {
                this.items = data.items || [];
                this.unreadCount = data.unread_count || 0;
                if (callback) callback();
            })
            .catch(() => { if (callback) callback(); });
        },

        handleClick(n) {
            // Mark as read
            if (!n.is_read) {
                fetch('/app/notifications/' + n.id + '/read', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json',
                    }
                }).then(() => {
                    n.is_read = true;
                    this.unreadCount = Math.max(0, this.unreadCount - 1);
                });
            }
            // Navigate to link
            if (n.url) {
                this.open = false;
                window.location.href = n.url;
            }
        },

        markAllRead() {
            fetch('/app/notifications/mark-all-read', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json',
                }
            }).then(() => {
                this.items = this.items.map(n => ({ ...n, is_read: true }));
                this.unreadCount = 0;
            });
        },
    };
}
</script>

{{-- ════════════════════════════════════════════════════════════
     AURA — AI Assistant Widget
     ════════════════════════════════════════════════════════════ --}}
<div id="aura-widget" x-data="auraChat()" x-init="init()" class="fixed bottom-6 right-6 z-[9999] font-sans">

    {{-- Floating button --}}
    <button @click="toggle()" id="aura-btn"
            class="relative w-14 h-14 rounded-full bg-gradient-to-br from-violet-600 to-indigo-600 shadow-lg hover:shadow-xl hover:scale-105 transition-all duration-200 flex items-center justify-center group"
            title="Ask Aura AI">
        {{-- Pulse ring --}}
        <span class="absolute inset-0 rounded-full bg-violet-500 opacity-30 group-hover:opacity-0 transition animate-ping"></span>
        {{-- Icon: AI sparkle --}}
        <svg x-show="!open" class="w-7 h-7 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                  d="M9.813 15.904L9 18.75l-.813-2.846a4.5 4.5 0 00-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 003.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 003.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 00-3.09 3.09z"/>
        </svg>
        {{-- Close X --}}
        <svg x-show="open" class="w-6 h-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
        </svg>
        {{-- Insight badge --}}
        <span x-show="insightCount > 0" x-text="insightCount"
              class="absolute -top-1 -right-1 w-5 h-5 rounded-full bg-red-500 text-white text-xs flex items-center justify-center font-bold"></span>
    </button>

    {{-- Chat Panel --}}
    <div x-show="open" x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 translate-y-4 scale-95"
         x-transition:enter-end="opacity-100 translate-y-0 scale-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0 scale-95"
         class="absolute bottom-16 right-0 w-[380px] bg-white dark:bg-[#111118] rounded-2xl shadow-2xl border border-gray-200 dark:border-white/10 flex flex-col overflow-hidden"
         style="max-height: 600px;">

        {{-- Header --}}
        <div class="bg-gradient-to-r from-violet-600 to-indigo-600 px-4 py-3 flex items-center justify-between">
            <div class="flex items-center gap-2">
                <div class="w-8 h-8 rounded-full bg-white/20 flex items-center justify-center">
                    <svg class="w-4 h-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                              d="M9.813 15.904L9 18.75l-.813-2.846a4.5 4.5 0 00-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 003.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 003.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 00-3.09 3.09z"/>
                    </svg>
                </div>
                <div>
                    <p class="text-white font-semibold text-sm">Aura</p>
                    <p class="text-white/70 text-xs">Your ERP Intelligence</p>
                </div>
            </div>
            <div class="flex items-center gap-1">
                <button @click="activeTab='chat'" :class="activeTab==='chat'?'bg-white/20 text-white':'text-white/60 hover:text-white'"
                        class="px-2 py-1 rounded-lg text-xs transition">Chat</button>
                <button @click="activeTab='insights'; loadInsights()" :class="activeTab==='insights'?'bg-white/20 text-white':'text-white/60 hover:text-white'"
                        class="px-2 py-1 rounded-lg text-xs transition flex items-center gap-1">
                    Insights
                    <span x-show="insightCount > 0" x-text="insightCount"
                          class="bg-red-500 text-white rounded-full w-4 h-4 text-xs flex items-center justify-center font-bold"></span>
                </button>
            </div>
        </div>

        {{-- ── CHAT TAB ── --}}
        <div x-show="activeTab==='chat'" class="flex flex-col flex-1 overflow-hidden" style="max-height: 490px;">

            {{-- Quick action chips --}}
            <div class="px-3 pt-3 flex flex-wrap gap-1.5" x-show="messages.length === 0">
                <template x-for="chip in quickChips" :key="chip.label">
                    <button @click="sendMessage(chip.prompt)"
                            class="px-2.5 py-1 text-xs rounded-full border border-violet-200 dark:border-violet-800/50 text-violet-700 dark:text-violet-300 bg-violet-50 dark:bg-violet-900/20 hover:bg-violet-100 dark:hover:bg-violet-900/40 transition">
                        <span x-text="chip.label"></span>
                    </button>
                </template>
                <p class="w-full text-xs text-gray-400 dark:text-gray-500 mt-1 px-0.5">Ask anything about your ERP</p>
            </div>

            {{-- Messages --}}
            <div class="flex-1 overflow-y-auto px-3 py-3 space-y-3" id="aura-messages" x-ref="msgContainer">
                <template x-for="(msg, i) in messages" :key="i">
                    <div :class="msg.role==='user' ? 'flex justify-end' : 'flex justify-start'">
                        <div :class="msg.role==='user'
                            ? 'bg-gradient-to-br from-violet-600 to-indigo-600 text-white rounded-2xl rounded-br-sm px-3 py-2 max-w-[80%] text-sm'
                            : 'bg-gray-100 dark:bg-white/5 text-gray-800 dark:text-gray-200 rounded-2xl rounded-bl-sm px-3 py-2 max-w-[85%] text-sm aura-prose'">
                            <div x-html="renderMarkdown(msg.content)"></div>
                        </div>
                    </div>
                </template>

                {{-- Typing indicator --}}
                <div x-show="loading" class="flex justify-start">
                    <div class="bg-gray-100 dark:bg-white/5 rounded-2xl rounded-bl-sm px-4 py-3">
                        <div class="flex gap-1">
                            <span class="w-1.5 h-1.5 bg-violet-400 rounded-full animate-bounce" style="animation-delay:0ms"></span>
                            <span class="w-1.5 h-1.5 bg-violet-400 rounded-full animate-bounce" style="animation-delay:150ms"></span>
                            <span class="w-1.5 h-1.5 bg-violet-400 rounded-full animate-bounce" style="animation-delay:300ms"></span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Input --}}
            <div class="px-3 pb-3 pt-1 border-t border-gray-100 dark:border-white/5">
                <form @submit.prevent="sendMessage(input); input=''" class="flex gap-2">
                    <input x-model="input" type="text" placeholder="Ask Aura anything…" maxlength="500"
                           :disabled="loading"
                           class="flex-1 px-3 py-2 text-sm bg-gray-100 dark:bg-white/5 border-0 rounded-xl text-gray-900 dark:text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-violet-400 dark:focus:ring-violet-600 disabled:opacity-60">
                    <button type="submit" :disabled="loading || !input.trim()"
                            class="w-9 h-9 bg-gradient-to-br from-violet-600 to-indigo-600 text-white rounded-xl flex items-center justify-center hover:opacity-90 disabled:opacity-40 transition">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 12L3.269 3.126A59.768 59.768 0 0121.485 12 59.77 59.77 0 013.27 20.876L5.999 12zm0 0h7.5"/>
                        </svg>
                    </button>
                </form>
                <button x-show="messages.length > 0" @click="messages = []" class="text-xs text-gray-400 dark:text-gray-500 hover:text-gray-600 dark:hover:text-gray-300 mt-1 transition">Clear chat</button>
            </div>
        </div>

        {{-- ── INSIGHTS TAB ── --}}
        <div x-show="activeTab==='insights'" class="flex-1 overflow-y-auto p-4 space-y-3" style="max-height: 490px;">
            <div x-show="insightsLoading" class="flex justify-center py-8">
                <div class="flex gap-1">
                    <span class="w-2 h-2 bg-violet-400 rounded-full animate-bounce" style="animation-delay:0ms"></span>
                    <span class="w-2 h-2 bg-violet-400 rounded-full animate-bounce" style="animation-delay:150ms"></span>
                    <span class="w-2 h-2 bg-violet-400 rounded-full animate-bounce" style="animation-delay:300ms"></span>
                </div>
            </div>

            <template x-for="(ins, i) in insights" :key="i">
                <div :class="ins.type==='warning'
                    ? 'bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-800/50 rounded-xl p-3'
                    : 'bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800/50 rounded-xl p-3'">
                    <p class="text-sm font-medium"
                       :class="ins.type==='warning' ? 'text-amber-800 dark:text-amber-300' : 'text-blue-800 dark:text-blue-300'"
                       x-text="(ins.type==='warning' ? '⚠️ ' : 'ℹ️ ') + ins.text"></p>
                    <a :href="ins.link" class="inline-block mt-1.5 text-xs font-medium underline"
                       :class="ins.type==='warning' ? 'text-amber-600 dark:text-amber-400' : 'text-blue-600 dark:text-blue-400'"
                       x-text="ins.label + ' →'"></a>
                </div>
            </template>

            <div x-show="!insightsLoading && insights.length === 0"
                 class="text-center py-8 text-gray-400 dark:text-gray-500 text-sm">
                <svg class="w-10 h-10 mx-auto mb-2 opacity-40" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                          d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                All looks good! No issues found.
            </div>

            {{-- ERP snapshot --}}
            <div x-show="snapshot && !insightsLoading"
                 class="mt-3 bg-gray-50 dark:bg-white/[0.02] border border-gray-100 dark:border-white/5 rounded-xl p-3">
                <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide mb-2">ERP Snapshot</p>
                <div class="grid grid-cols-2 gap-2">
                    <div class="text-center">
                        <p class="text-lg font-bold text-gray-900 dark:text-white" x-text="snapshot.total_employees ?? '—'"></p>
                        <p class="text-xs text-gray-500 dark:text-gray-400">Employees</p>
                    </div>
                    <div class="text-center">
                        <p class="text-lg font-bold text-yellow-600 dark:text-yellow-400" x-text="snapshot.open_tickets ?? '—'"></p>
                        <p class="text-xs text-gray-500 dark:text-gray-400">Open Tickets</p>
                    </div>
                    <div class="text-center">
                        <p class="text-lg font-bold text-blue-600 dark:text-blue-400" x-text="snapshot.payslips_this_month ?? '—'"></p>
                        <p class="text-xs text-gray-500 dark:text-gray-400">Payslips This Month</p>
                    </div>
                    <div class="text-center">
                        <p class="text-lg font-bold text-green-600 dark:text-green-400" x-text="snapshot.net_payroll_month ?? '—'"></p>
                        <p class="text-xs text-gray-500 dark:text-gray-400">Net Payroll</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.aura-prose strong { font-weight: 600; }
.aura-prose ul { list-style: disc; padding-left: 1rem; margin-top: 0.25rem; }
.aura-prose li { margin-bottom: 0.15rem; }
.aura-prose p { margin-bottom: 0.25rem; }
</style>

<script>
function auraChat() {
    return {
        open: false,
        activeTab: 'chat',
        messages: [],
        input: '',
        loading: false,
        insights: [],
        insightCount: 0,
        insightsLoading: false,
        snapshot: null,

        quickChips: [
            { label: '📊 Business snapshot',      prompt: 'Give me a quick business snapshot of the ERP right now.' },
            { label: '🎫 Open tickets summary',   prompt: 'Summarize the current helpdesk ticket situation.' },
            { label: '💰 Payroll status',         prompt: 'What is the payroll status for this month?' },
            { label: '👥 Employee overview',      prompt: 'Give me an overview of the workforce.' },
            { label: '✍️ Draft HR letter',        prompt: 'Draft a professional employee welcome letter template.' },
            { label: '⚠️ Any risks?',             prompt: 'Are there any risks or issues I should address urgently?' },
        ],

        init() {
            // Load insight count on startup
            fetch('/app/ai/insights', { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
                .then(r => r.json())
                .then(d => { this.insightCount = (d.insights || []).length; })
                .catch(() => {});
        },

        toggle() { this.open = !this.open; },

        async sendMessage(text) {
            text = (text || this.input || '').trim();
            if (!text || this.loading) return;
            this.input = '';
            this.messages.push({ role: 'user', content: text });
            this.loading = true;
            this.$nextTick(() => this.scrollToBottom());

            try {
                const history = this.messages.slice(0, -1).map(m => ({ role: m.role, content: m.content }));
                const res = await fetch('/app/ai/chat', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                    body: JSON.stringify({ message: text, history }),
                });
                const data = await res.json();
                this.messages.push({ role: 'assistant', content: data.reply || 'No response.' });
            } catch (e) {
                this.messages.push({ role: 'assistant', content: '⚠️ Network error. Please try again.' });
            }

            this.loading = false;
            this.$nextTick(() => this.scrollToBottom());
        },

        async loadInsights() {
            this.insightsLoading = true;
            try {
                const res  = await fetch('/app/ai/insights', { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
                const data = await res.json();
                this.insights      = data.insights || [];
                this.snapshot      = data.context  || null;
                this.insightCount  = this.insights.length;
            } catch (e) {}
            this.insightsLoading = false;
        },

        scrollToBottom() {
            const el = document.getElementById('aura-messages');
            if (el) el.scrollTop = el.scrollHeight;
        },

        renderMarkdown(text) {
            if (!text) return '';
            return text
                .replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;')
                // bold
                .replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>')
                // inline code
                .replace(/`([^`]+)`/g, '<code class="bg-black/10 dark:bg-white/10 px-1 rounded text-xs font-mono">$1</code>')
                // headings
                .replace(/^### (.*$)/gm, '<p class="font-semibold text-sm mt-2 mb-0.5">$1</p>')
                .replace(/^## (.*$)/gm,  '<p class="font-bold text-sm mt-2 mb-1">$1</p>')
                // bullet lists
                .replace(/^[-*•] (.*)$/gm, '<li>$1</li>')
                .replace(/(<li>.*<\/li>)/s, '<ul class="list-disc pl-4 space-y-0.5 my-1">$1</ul>')
                // line breaks
                .replace(/\n\n/g, '</p><p class="mt-1">')
                .replace(/\n/g, '<br>');
        },
    };
}
</script>
</body>
</html>

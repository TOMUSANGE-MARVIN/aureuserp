@extends('app.layouts.app')

@section('title', 'Overview')
@section('breadcrumb', 'Overview')

@section('head_scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.2/dist/chart.umd.min.js"></script>
@endsection

@section('topbar_actions')
@if($hasRealData === false)
<span class="demo-badge hidden sm:inline-flex">
    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
    Demo data
</span>
@endif
@endsection

@section('content')
<!-- Page header -->
<div class="flex items-center justify-between">
    <div>
        <h1 class="text-xl font-bold text-gray-900 dark:text-white">Good {{ date('H') < 12 ? 'morning' : (date('H') < 17 ? 'afternoon' : 'evening') }}, {{ explode(' ', Auth::user()?->name ?? 'there')[0] }} 👋</h1>
        <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">{{ date('l, F j, Y') }} · Here's what's happening today.</p>
    </div>
    <div class="flex gap-2">
        <select class="text-sm bg-white dark:bg-[#1a1a24] border border-gray-200 dark:border-white/8 rounded-xl px-3 py-2 text-gray-700 dark:text-gray-300 focus:outline-none focus:border-brand-400">
            <option>This Year</option>
            <option>Last Year</option>
            <option>Last 6 Months</option>
        </select>
        <a href="/admin" class="flex items-center gap-2 bg-brand-600 hover:bg-brand-700 text-white text-sm font-medium px-4 py-2 rounded-xl transition-colors shadow-sm">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            New Record
        </a>
    </div>
</div>

<!-- ── Stat Cards ── -->
<div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
    @php
    $stats = [
        ['label'=>'Total Revenue','value'=>'$'.number_format($totalRevenue/1000,1).'K','change'=>'+12.5%','up'=>true,
         'color'=>'brand','icon'=>'<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>'],
        ['label'=>'Sales Orders','value'=>number_format($totalOrders),'change'=>'+8.2%','up'=>true,
         'color'=>'blue','icon'=>'<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>'],
        ['label'=>'Customers','value'=>number_format($totalCustomers),'change'=>'+5.1%','up'=>true,
         'color'=>'emerald','icon'=>'<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0"/>'],
        ['label'=>'Employees','value'=>number_format($totalEmployees),'change'=>'+2','up'=>true,
         'color'=>'orange','icon'=>'<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>'],
    ];
    $colorMap = [
        'brand'   => ['bg'=>'bg-brand-50 dark:bg-brand-900/20',    'icon'=>'text-brand-600 dark:text-brand-400',   'bar'=>'bg-brand-500'],
        'blue'    => ['bg'=>'bg-blue-50 dark:bg-blue-900/20',      'icon'=>'text-blue-600 dark:text-blue-400',     'bar'=>'bg-blue-500'],
        'emerald' => ['bg'=>'bg-emerald-50 dark:bg-emerald-900/20','icon'=>'text-emerald-600 dark:text-emerald-400','bar'=>'bg-emerald-500'],
        'orange'  => ['bg'=>'bg-orange-50 dark:bg-orange-900/20',  'icon'=>'text-orange-600 dark:text-orange-400', 'bar'=>'bg-orange-500'],
    ];
    @endphp
    @foreach($stats as $stat)
    @php $c = $colorMap[$stat['color']]; @endphp
    <div class="stat-card bg-white dark:bg-[#16161e] rounded-2xl border border-gray-100 dark:border-white/6 p-5 shadow-sm">
        <div class="flex items-start justify-between mb-4">
            <div class="{{ $c['bg'] }} rounded-xl p-2.5">
                <svg class="w-5 h-5 {{ $c['icon'] }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">{!! $stat['icon'] !!}</svg>
            </div>
            <span class="badge {{ $stat['up'] ? 'badge-up' : 'badge-down' }}">{{ $stat['change'] }}</span>
        </div>
        <div class="text-2xl font-bold text-gray-900 dark:text-white mb-0.5">{{ $stat['value'] }}</div>
        <div class="text-xs text-gray-500 dark:text-gray-400">{{ $stat['label'] }}</div>
        <div class="mt-3 h-1 bg-gray-100 dark:bg-white/5 rounded-full overflow-hidden">
            <div class="{{ $c['bar'] }} h-full rounded-full opacity-60" style="width: {{ rand(45,85) }}%"></div>
        </div>
    </div>
    @endforeach
</div>

<!-- ── Secondary stat strip ── -->
<div class="grid grid-cols-3 gap-4">
    @php $secondaryStats = [
        ['label'=>'Products','value'=>$totalProducts ?: '0','sub'=>'in catalog',
         'icon'=>'<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>'],
        ['label'=>'Active Projects','value'=>$totalProjects ?: '0','sub'=>'in progress',
         'icon'=>'<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>'],
        ['label'=>'Open Tasks','value'=>$openTasks ?: '0','sub'=>'pending',
         'icon'=>'<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>'],
    ]; @endphp
    @foreach($secondaryStats as $s)
    <div class="bg-white dark:bg-[#16161e] rounded-2xl border border-gray-100 dark:border-white/6 p-4 flex items-center gap-4 shadow-sm">
        <div class="bg-gray-50 dark:bg-white/5 rounded-xl p-2.5 flex-shrink-0">
            <svg class="text-gray-500 dark:text-gray-400" style="width:18px;height:18px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">{!! $s['icon'] !!}</svg>
        </div>
        <div class="min-w-0">
            <div class="text-xl font-bold text-gray-900 dark:text-white">{{ $s['value'] }}</div>
            <div class="text-xs text-gray-400 truncate">{{ $s['label'] }}</div>
        </div>
    </div>
    @endforeach
</div>

<!-- ── Charts Row 1: Revenue Line + Order Donut ── -->
<div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
    <!-- Revenue Line Chart -->
    <div class="lg:col-span-2 chart-card bg-white dark:bg-[#16161e] rounded-2xl border border-gray-100 dark:border-white/6 p-5 shadow-sm">
        <div class="flex items-center justify-between mb-5">
            <div>
                <h3 class="font-semibold text-gray-900 dark:text-white text-sm">Revenue Overview</h3>
                <p class="text-xs text-gray-400 mt-0.5">Monthly revenue vs target · {{ date('Y') }}</p>
            </div>
            <div class="flex items-center gap-4">
                <span class="flex items-center gap-1.5 text-xs text-gray-400">
                    <span class="w-3 h-0.5 bg-brand-500 rounded-full inline-block"></span> Actual
                </span>
                <span class="flex items-center gap-1.5 text-xs text-gray-400">
                    <span class="w-3 h-0.5 bg-blue-400 rounded-full inline-block" style="border-top:2px dashed #60a5fa;height:0;"></span> Target
                </span>
            </div>
        </div>
        <div class="relative" style="height:220px;">
            <canvas id="revenueChart"></canvas>
        </div>
    </div>

    <!-- Order Status Donut -->
    <div class="chart-card bg-white dark:bg-[#16161e] rounded-2xl border border-gray-100 dark:border-white/6 p-5 shadow-sm flex flex-col">
        <div class="mb-4">
            <h3 class="font-semibold text-gray-900 dark:text-white text-sm">Order Status</h3>
            <p class="text-xs text-gray-400 mt-0.5">Distribution breakdown</p>
        </div>
        <div class="relative flex-1 flex items-center justify-center" style="min-height:140px;">
            <canvas id="donutChart"></canvas>
        </div>
        <div class="mt-4 space-y-2">
            @php
            $statusLabels = ['draft'=>'Draft','sent'=>'Sent','sale'=>'Confirmed','done'=>'Done','cancel'=>'Cancelled'];
            $statusColors = ['draft'=>'#94a3b8','sent'=>'#60a5fa','sale'=>'#7c3aed','done'=>'#34d399','cancel'=>'#f87171'];
            @endphp
            @foreach($orderStatuses->take(4) as $state => $cnt)
            <div class="flex items-center justify-between text-xs">
                <div class="flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full flex-shrink-0" style="background:{{ $statusColors[$state] ?? '#94a3b8' }}"></span>
                    <span class="text-gray-600 dark:text-gray-400">{{ $statusLabels[$state] ?? ucfirst($state) }}</span>
                </div>
                <span class="font-semibold text-gray-800 dark:text-gray-200">{{ $cnt }}</span>
            </div>
            @endforeach
        </div>
    </div>
</div>

<!-- ── Charts Row 2: Sales Bar + Customers Area + Tasks ── -->
<div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
    <div class="chart-card bg-white dark:bg-[#16161e] rounded-2xl border border-gray-100 dark:border-white/6 p-5 shadow-sm">
        <div class="mb-4">
            <h3 class="font-semibold text-gray-900 dark:text-white text-sm">Monthly Orders</h3>
            <p class="text-xs text-gray-400 mt-0.5">Orders placed per month</p>
        </div>
        <div class="relative" style="height:200px;">
            <canvas id="salesBarChart"></canvas>
        </div>
    </div>

    <div class="chart-card bg-white dark:bg-[#16161e] rounded-2xl border border-gray-100 dark:border-white/6 p-5 shadow-sm">
        <div class="mb-4">
            <h3 class="font-semibold text-gray-900 dark:text-white text-sm">Customer Growth</h3>
            <p class="text-xs text-gray-400 mt-0.5">New customers per month</p>
        </div>
        <div class="relative" style="height:200px;">
            <canvas id="customersAreaChart"></canvas>
        </div>
    </div>

    <div class="chart-card bg-white dark:bg-[#16161e] rounded-2xl border border-gray-100 dark:border-white/6 p-5 shadow-sm">
        <div class="mb-4">
            <h3 class="font-semibold text-gray-900 dark:text-white text-sm">Tasks by Stage</h3>
            <p class="text-xs text-gray-400 mt-0.5">Project task distribution</p>
        </div>
        <div class="relative" style="height:200px;">
            <canvas id="tasksChart"></canvas>
        </div>
    </div>
</div>

<!-- ── Charts Row 3: Category Pie + Revenue by Month ── -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
    <div class="bg-white dark:bg-[#16161e] rounded-2xl border border-gray-100 dark:border-white/6 p-5 shadow-sm">
        <div class="flex items-start justify-between mb-5">
            <div>
                <h3 class="font-semibold text-gray-900 dark:text-white text-sm">Product Categories</h3>
                <p class="text-xs text-gray-400 mt-0.5">Inventory by category</p>
            </div>
        </div>
        <div class="flex items-center gap-6">
            <div class="relative flex-shrink-0" style="width:160px;height:160px;">
                <canvas id="categoryPieChart"></canvas>
            </div>
            <div class="space-y-2 flex-1 min-w-0">
                @foreach($inventoryCategories->take(6) as $cat => $val)
                @php $palette = ['#7c3aed','#60a5fa','#34d399','#f59e0b','#f87171','#a78bfa']; $idx = $loop->index; @endphp
                <div class="flex items-center gap-2 text-xs">
                    <span class="w-2 h-2 rounded-sm flex-shrink-0" style="background:{{ $palette[$idx % 6] }}"></span>
                    <span class="text-gray-600 dark:text-gray-400 truncate flex-1">{{ $cat }}</span>
                    <span class="font-semibold text-gray-800 dark:text-gray-200">{{ $val }}</span>
                </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Recent Orders Table -->
    <div class="bg-white dark:bg-[#16161e] rounded-2xl border border-gray-100 dark:border-white/6 p-5 shadow-sm">
        <div class="flex items-center justify-between mb-4">
            <div>
                <h3 class="font-semibold text-gray-900 dark:text-white text-sm">Recent Orders</h3>
                <p class="text-xs text-gray-400 mt-0.5">Latest sales activity</p>
            </div>
            <a href="/admin/sales/orders" class="text-xs text-brand-600 dark:text-brand-400 font-medium hover:underline">View all →</a>
        </div>
        <div class="space-y-2">
            @php
            $statusBadges = [
                'draft'  => 'badge-neutral',
                'sent'   => 'bg-blue-50 dark:bg-blue-900/20 text-blue-600 dark:text-blue-400',
                'sale'   => 'bg-brand-50 dark:bg-brand-900/20 text-brand-700 dark:text-brand-400',
                'done'   => 'badge-up',
                'cancel' => 'badge-down',
            ];
            @endphp
            @forelse($recentOrders as $order)
            <div class="flex items-center gap-3 py-2 border-b border-gray-50 dark:border-white/4 last:border-0">
                <div class="w-8 h-8 rounded-xl bg-brand-50 dark:bg-brand-900/20 flex items-center justify-center flex-shrink-0">
                    <svg class="w-4 h-4 text-brand-600 dark:text-brand-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div class="flex-1 min-w-0">
                    <div class="text-xs font-semibold text-gray-800 dark:text-gray-200 truncate">{{ $order->name ?? 'Order #'.$order->id }}</div>
                    <div class="text-[11px] text-gray-400 truncate">{{ optional($order->date_order ?? null)->format('M d, Y') ?? '—' }}</div>
                </div>
                <div class="text-right flex-shrink-0">
                    <div class="text-xs font-bold text-gray-900 dark:text-white">${{ number_format($order->amount_total ?? 0) }}</div>
                    <span class="badge {{ $statusBadges[$order->state ?? ''] ?? 'badge-neutral' }} text-[10px]">{{ ucfirst($order->state ?? 'draft') }}</span>
                </div>
            </div>
            @empty
            <p class="text-xs text-gray-400 text-center py-4">No recent orders · <a href="/admin/sales/orders/create" class="text-brand-600 hover:underline">Create one</a></p>
            @endforelse
        </div>
    </div>
</div>

<!-- ── Quick Actions ── -->
<div class="bg-white dark:bg-[#16161e] rounded-2xl border border-gray-100 dark:border-white/6 p-5 shadow-sm">
    <h3 class="font-semibold text-gray-900 dark:text-white text-sm mb-4">Quick Actions</h3>
    <div class="grid grid-cols-2 sm:grid-cols-4 lg:grid-cols-8 gap-3">
        @php
        $quickActions = [
            ['New Invoice',   '/admin/accounting/accounting/journal-entries/create', '#7c3aed',
             '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>'],
            ['New Order',     '/admin/sales/orders/create', '#2563eb',
             '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M16 8v8m-4-5v5m-4-2v2m-2 4h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>'],
            ['New Customer',  '/admin/contacts/customers/create', '#059669',
             '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>'],
            ['New Product',   '/admin/products/products/create', '#d97706',
             '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>'],
            ['New Employee',  '/admin/employees/employees/create', '#7c3aed',
             '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>'],
            ['New Project',   '/admin/projects/projects/create', '#0891b2',
             '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>'],
            ['New Purchase',  '/admin/purchases/orders/create', '#be185d',
             '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M3 3h2l.4 2M7 13h10l4-8H5.4"/>'],
            ['Reports',       '/admin/accounting', '#374151',
             '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>'],
        ];
        @endphp
        @foreach($quickActions as $qa)
        <a href="{{ $qa[1] }}" class="flex flex-col items-center gap-2 p-3 rounded-xl bg-gray-50 dark:bg-white/4 hover:bg-brand-50 dark:hover:bg-brand-900/20 transition-colors group text-center">
            <div class="w-9 h-9 rounded-xl flex items-center justify-center" style="background:{{ $qa[2] }}18;">
                <svg style="width:18px;height:18px;color:{{ $qa[2] }};" fill="none" stroke="currentColor" viewBox="0 0 24 24">{!! $qa[3] !!}</svg>
            </div>
            <span class="text-[11px] font-medium text-gray-600 dark:text-gray-400 group-hover:text-gray-900 dark:group-hover:text-white leading-tight">{{ $qa[0] }}</span>
        </a>
        @endforeach
    </div>
</div>
@endsection

@section('scripts')
<script>
const isDark    = () => document.documentElement.classList.contains('dark');
const gridColor = () => isDark() ? 'rgba(255,255,255,0.05)' : 'rgba(0,0,0,0.05)';
const lblColor  = () => isDark() ? '#6b7280' : '#9ca3af';
const tip = () => ({
    backgroundColor: isDark() ? '#1a1a2e' : '#fff',
    titleColor: isDark() ? '#e5e7eb' : '#111',
    bodyColor:  isDark() ? '#9ca3af' : '#6b7280',
    borderColor: isDark() ? 'rgba(255,255,255,0.08)' : 'rgba(0,0,0,0.08)',
    borderWidth: 1, padding: 10,
});

const months       = @json($months->values());
const revenueData  = @json($revenueData->values());
const salesData    = @json($salesData->values());
const partnersData = @json($partnersData->values());
const orderLabels  = @json($orderStatuses->keys());
const orderValues  = @json($orderStatuses->values());
const taskLabels   = @json($taskStages->keys());
const taskValues   = @json($taskStages->values());
const catLabels    = @json($inventoryCategories->keys());
const catValues    = @json($inventoryCategories->values());
const palette = ['#7c3aed','#60a5fa','#34d399','#f59e0b','#f87171','#a78bfa','#2dd4bf','#fb923c'];

Chart.defaults.font.family = 'Inter, sans-serif';
Chart.defaults.font.size   = 11;

// 1. Revenue Line
const revCtx = document.getElementById('revenueChart').getContext('2d');
const revGrad = revCtx.createLinearGradient(0, 0, 0, 220);
revGrad.addColorStop(0, 'rgba(124,58,237,0.18)');
revGrad.addColorStop(1, 'rgba(124,58,237,0)');
new Chart(revCtx, {
    type: 'line',
    data: { labels: months, datasets: [
        { label: 'Revenue', data: revenueData, borderColor: '#7c3aed', backgroundColor: revGrad,
          borderWidth: 2.5, pointRadius: 3, pointBackgroundColor: '#7c3aed', pointBorderColor: '#fff', pointBorderWidth: 2, tension: 0.4, fill: true },
        { label: 'Target', data: revenueData.map(v => Math.round(v * 1.15)), borderColor: '#60a5fa',
          backgroundColor: 'transparent', borderWidth: 1.5, borderDash: [5,4], pointRadius: 0, tension: 0.4, fill: false },
    ]},
    options: {
        responsive: true, maintainAspectRatio: false,
        interaction: { intersect: false, mode: 'index' },
        plugins: { legend: { display: false }, tooltip: { ...tip(), callbacks: { label: ctx => ' $' + ctx.parsed.y.toLocaleString() } } },
        scales: {
            x: { grid: { color: gridColor(), drawBorder: false }, ticks: { color: lblColor() } },
            y: { grid: { color: gridColor(), drawBorder: false }, ticks: { color: lblColor(), callback: v => '$'+(v/1000).toFixed(0)+'k' } }
        }
    }
});

// 2. Donut
new Chart(document.getElementById('donutChart'), {
    type: 'doughnut',
    data: { labels: orderLabels, datasets: [{ data: orderValues, backgroundColor: palette, borderWidth: 0, hoverOffset: 6 }] },
    options: { responsive: true, maintainAspectRatio: false, cutout: '74%', plugins: { legend: { display: false }, tooltip: tip() } }
});

// 3. Sales Bar
new Chart(document.getElementById('salesBarChart'), {
    type: 'bar',
    data: { labels: months, datasets: [{
        label: 'Orders', data: salesData,
        backgroundColor: months.map((_, i) => i === new Date().getMonth() ? '#7c3aed' : 'rgba(124,58,237,0.22)'),
        borderRadius: 6, borderSkipped: false,
    }]},
    options: {
        responsive: true, maintainAspectRatio: false,
        plugins: { legend: { display: false }, tooltip: tip() },
        scales: {
            x: { grid: { display: false }, ticks: { color: lblColor() } },
            y: { grid: { color: gridColor() }, ticks: { color: lblColor() } }
        }
    }
});

// 4. Customers Area
const custCtx = document.getElementById('customersAreaChart').getContext('2d');
const custGrad = custCtx.createLinearGradient(0, 0, 0, 200);
custGrad.addColorStop(0, 'rgba(52,211,153,0.2)');
custGrad.addColorStop(1, 'rgba(52,211,153,0)');
new Chart(custCtx, {
    type: 'line',
    data: { labels: months, datasets: [{
        label: 'New Customers', data: partnersData, borderColor: '#34d399', backgroundColor: custGrad,
        borderWidth: 2, pointRadius: 3, pointBackgroundColor: '#34d399', pointBorderColor: '#fff', pointBorderWidth: 2, tension: 0.4, fill: true,
    }]},
    options: {
        responsive: true, maintainAspectRatio: false,
        plugins: { legend: { display: false }, tooltip: tip() },
        scales: {
            x: { grid: { display: false }, ticks: { color: lblColor() } },
            y: { grid: { color: gridColor() }, ticks: { color: lblColor() } }
        }
    }
});

// 5. Tasks Horizontal Bar
new Chart(document.getElementById('tasksChart'), {
    type: 'bar',
    data: { labels: taskLabels, datasets: [{
        data: taskValues, backgroundColor: palette.slice(0, taskLabels.length),
        borderRadius: 5, borderSkipped: false,
    }]},
    options: {
        responsive: true, maintainAspectRatio: false, indexAxis: 'y',
        plugins: { legend: { display: false }, tooltip: tip() },
        scales: {
            x: { grid: { color: gridColor() }, ticks: { color: lblColor() } },
            y: { grid: { display: false }, ticks: { color: lblColor() } }
        }
    }
});

// 6. Category Pie
new Chart(document.getElementById('categoryPieChart'), {
    type: 'pie',
    data: { labels: catLabels, datasets: [{
        data: catValues, backgroundColor: palette,
        borderWidth: 2, borderColor: isDark() ? '#16161e' : '#fff', hoverOffset: 6,
    }]},
    options: {
        responsive: true, maintainAspectRatio: false,
        plugins: { legend: { display: false }, tooltip: tip() }
    }
});
</script>
@endsection

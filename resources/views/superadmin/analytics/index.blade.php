@extends('superadmin.layouts.app')
@section('title', 'Analytics')
@section('breadcrumb', 'Analytics')

@section('content')
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-xl font-bold text-gray-900 dark:text-white">Platform Analytics</h1>
            <p class="text-sm text-gray-500 mt-1">Growth, revenue and subscription trends</p>
        </div>
    </div>

    <!-- KPI strip -->
    <div class="grid grid-cols-3 gap-4">
        <div class="bg-white dark:bg-[#0f0f1a] rounded-2xl border border-gray-100 dark:border-white/5 px-5 py-4 flex items-center gap-4">
            <div class="w-10 h-10 bg-indigo-50 dark:bg-indigo-900/20 rounded-xl flex items-center justify-center">
                <svg class="w-5 h-5 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16"/></svg>
            </div>
            <div>
                <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $totalOrgs }}</p>
                <p class="text-xs text-gray-500">Total Organizations</p>
            </div>
        </div>
        <div class="bg-white dark:bg-[#0f0f1a] rounded-2xl border border-gray-100 dark:border-white/5 px-5 py-4 flex items-center gap-4">
            <div class="w-10 h-10 bg-emerald-50 dark:bg-emerald-900/20 rounded-xl flex items-center justify-center">
                <svg class="w-5 h-5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2"/></svg>
            </div>
            <div>
                <p class="text-2xl font-bold text-gray-900 dark:text-white">${{ number_format($mrr, 0) }}</p>
                <p class="text-xs text-gray-500">Monthly Revenue</p>
            </div>
        </div>
        <div class="bg-white dark:bg-[#0f0f1a] rounded-2xl border border-gray-100 dark:border-white/5 px-5 py-4 flex items-center gap-4">
            <div class="w-10 h-10 bg-blue-50 dark:bg-blue-900/20 rounded-xl flex items-center justify-center">
                <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2"/></svg>
            </div>
            <div>
                <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $totalUsers }}</p>
                <p class="text-xs text-gray-500">Platform Users</p>
            </div>
        </div>
    </div>

    <!-- MRR Line Chart -->
    <div class="bg-white dark:bg-[#0f0f1a] rounded-2xl border border-gray-100 dark:border-white/5 p-6">
        <div class="flex items-center justify-between mb-5">
            <div>
                <h2 class="font-semibold text-gray-900 dark:text-white">MRR Trend</h2>
                <p class="text-xs text-gray-400 mt-0.5">Monthly recurring revenue over the last 12 months</p>
            </div>
            <span class="text-xs text-emerald-600 dark:text-emerald-400 bg-emerald-50 dark:bg-emerald-900/20 px-3 py-1 rounded-full font-medium">MRR</span>
        </div>
        <canvas id="mrrTrendChart" height="80"></canvas>
    </div>

    <!-- Org Growth Bar Chart -->
    <div class="bg-white dark:bg-[#0f0f1a] rounded-2xl border border-gray-100 dark:border-white/5 p-6">
        <div class="flex items-center justify-between mb-5">
            <div>
                <h2 class="font-semibold text-gray-900 dark:text-white">New Organizations Per Month</h2>
                <p class="text-xs text-gray-400 mt-0.5">Growth over the last 12 months</p>
            </div>
            <span class="text-xs text-indigo-600 dark:text-indigo-400 bg-indigo-50 dark:bg-indigo-900/20 px-3 py-1 rounded-full font-medium">Orgs</span>
        </div>
        <canvas id="orgGrowthChart" height="80"></canvas>
    </div>

    <!-- Bottom row: Subscription Status + Plan Distribution -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">
        <div class="bg-white dark:bg-[#0f0f1a] rounded-2xl border border-gray-100 dark:border-white/5 p-6">
            <h2 class="font-semibold text-gray-900 dark:text-white mb-4">Subscription Status</h2>
            @php $statusColors = ['active'=>'#10b981','trial'=>'#3b82f6','cancelled'=>'#ef4444','expired'=>'#9ca3af']; @endphp
            <div class="flex justify-center mb-4">
                <canvas id="statusChart" width="180" height="180"></canvas>
            </div>
            <div class="space-y-2">
                @foreach($statusDist as $sd)
                <div class="flex items-center justify-between text-sm">
                    <div class="flex items-center gap-2">
                        <div class="w-2.5 h-2.5 rounded-full" style="background:{{ $statusColors[$sd->status] ?? '#9ca3af' }}"></div>
                        <span class="capitalize text-gray-700 dark:text-gray-300">{{ $sd->status }}</span>
                    </div>
                    <span class="font-semibold text-gray-900 dark:text-white">{{ $sd->count }}</span>
                </div>
                @endforeach
                @if($statusDist->isEmpty())<p class="text-xs text-gray-400 text-center py-3">No subscriptions yet.</p>@endif
            </div>
        </div>

        <div class="bg-white dark:bg-[#0f0f1a] rounded-2xl border border-gray-100 dark:border-white/5 p-6">
            <h2 class="font-semibold text-gray-900 dark:text-white mb-4">Plan Distribution</h2>
            <div class="flex justify-center mb-4">
                <canvas id="planChart" width="180" height="180"></canvas>
            </div>
            <div class="space-y-2">
                @php $pColors = ['#6366f1','#10b981','#3b82f6','#f59e0b','#ef4444']; @endphp
                @foreach($planDistribution as $i => $pd)
                <div class="flex items-center justify-between text-sm">
                    <div class="flex items-center gap-2">
                        <div class="w-2.5 h-2.5 rounded-full" style="background:{{ $pColors[$i % count($pColors)] }}"></div>
                        <span class="text-gray-700 dark:text-gray-300">{{ $pd->plan_name }}</span>
                    </div>
                    <span class="font-semibold text-gray-900 dark:text-white">{{ $pd->count }}</span>
                </div>
                @endforeach
                @if($planDistribution->isEmpty())<p class="text-xs text-gray-400 text-center py-3">No subscriptions yet.</p>@endif
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <script>
        const isDark     = document.documentElement.classList.contains('dark');
        const gridColor  = isDark ? 'rgba(255,255,255,0.05)' : 'rgba(0,0,0,0.06)';
        const labelColor = isDark ? '#9ca3af' : '#6b7280';

        const axisBase = {
            grid:  { color: gridColor },
            ticks: { color: labelColor, font: { size: 11 } }
        };

        new Chart(document.getElementById('mrrTrendChart'), {
            type: 'line',
            data: {
                labels: @json($mrrGrowth['labels']),
                datasets: [{
                    label: 'MRR ($)',
                    data: @json($mrrGrowth['values']),
                    borderColor: '#10b981', backgroundColor: 'rgba(16,185,129,0.08)',
                    borderWidth: 2, pointRadius: 4, pointBackgroundColor: '#10b981',
                    fill: true, tension: 0.4
                }]
            },
            options: {
                responsive: true,
                plugins: { legend: { display: false } },
                scales: {
                    x: axisBase,
                    y: { ...axisBase, ticks: { ...axisBase.ticks, callback: v => '$'+v.toLocaleString() } }
                }
            }
        });

        new Chart(document.getElementById('orgGrowthChart'), {
            type: 'bar',
            data: {
                labels: @json($orgGrowth['labels']),
                datasets: [{
                    label: 'New Orgs',
                    data: @json($orgGrowth['values']),
                    backgroundColor: 'rgba(99,102,241,0.75)',
                    borderRadius: 6, borderSkipped: false
                }]
            },
            options: {
                responsive: true,
                plugins: { legend: { display: false } },
                scales: { x: axisBase, y: { ...axisBase, beginAtZero: true } }
            }
        });

        @if($statusDist->isNotEmpty())
        new Chart(document.getElementById('statusChart'), {
            type: 'doughnut',
            data: {
                labels: @json($statusDist->pluck('status')),
                datasets: [{
                    data: @json($statusDist->pluck('count')),
                    backgroundColor: @json($statusDist->map(fn($s) => $statusColors[$s->status] ?? '#9ca3af')),
                    borderWidth: 0, hoverOffset: 4
                }]
            },
            options: { responsive:false, cutout:'62%', plugins:{ legend:{ display:false } } }
        });
        @endif

        @if($planDistribution->isNotEmpty())
        new Chart(document.getElementById('planChart'), {
            type: 'doughnut',
            data: {
                labels: @json($planDistribution->pluck('plan_name')),
                datasets: [{
                    data: @json($planDistribution->pluck('count')),
                    backgroundColor: ['#6366f1','#10b981','#3b82f6','#f59e0b','#ef4444'],
                    borderWidth: 0, hoverOffset: 4
                }]
            },
            options: { responsive:false, cutout:'62%', plugins:{ legend:{ display:false } } }
        });
        @endif
    </script>
    @php $statusColors = ['active'=>'#10b981','trial'=>'#3b82f6','cancelled'=>'#ef4444','expired'=>'#9ca3af']; @endphp
@endsection

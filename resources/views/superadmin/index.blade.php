@extends('superadmin.layouts.app')
@section('title', 'Admin Console')
@section('breadcrumb', 'Dashboard')

@section('content')
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Admin Console</h1>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Platform overview — {{ now()->format('l, F j Y') }}</p>
        </div>
        <div class="flex items-center gap-2 px-3 py-1.5 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-xl">
            <div class="w-2 h-2 rounded-full bg-green-500 animate-pulse"></div>
            <span class="text-xs font-medium text-green-700 dark:text-green-400">Platform Online</span>
        </div>
    </div>

    <!-- Key Metrics -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Organizations -->
        <div class="bg-white dark:bg-[#0f0f1a] rounded-2xl border border-gray-100 dark:border-white/5 p-5 group hover:border-indigo-200 dark:hover:border-indigo-900/50 transition-colors">
            <div class="flex items-center justify-between mb-3">
                <div class="w-9 h-9 bg-indigo-50 dark:bg-indigo-900/30 rounded-xl flex items-center justify-center">
                    <svg class="w-5 h-5 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                    </svg>
                </div>
                <span class="text-xs font-medium text-green-600 dark:text-green-400 bg-green-50 dark:bg-green-900/20 px-2 py-0.5 rounded-full">{{ $stats['active_orgs'] }} active</span>
            </div>
            <p class="text-3xl font-bold text-gray-900 dark:text-white">{{ $stats['total_orgs'] }}</p>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Organizations</p>
            <p class="text-xs text-gray-400 dark:text-gray-500 mt-0.5">{{ $stats['trial_orgs'] }} on trial · {{ $stats['suspended_orgs'] }} suspended</p>
        </div>

        <!-- MRR -->
        <div class="bg-white dark:bg-[#0f0f1a] rounded-2xl border border-gray-100 dark:border-white/5 p-5 group hover:border-emerald-200 dark:hover:border-emerald-900/50 transition-colors">
            <div class="flex items-center justify-between mb-3">
                <div class="w-9 h-9 bg-emerald-50 dark:bg-emerald-900/30 rounded-xl flex items-center justify-center">
                    <svg class="w-5 h-5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <span class="text-xs font-medium text-emerald-600 dark:text-emerald-400 bg-emerald-50 dark:bg-emerald-900/20 px-2 py-0.5 rounded-full">Monthly</span>
            </div>
            <p class="text-3xl font-bold text-gray-900 dark:text-white">${{ number_format($stats['mrr'], 0) }}</p>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">MRR</p>
            <p class="text-xs text-gray-400 dark:text-gray-500 mt-0.5">ARR ≈ ${{ number_format($stats['arr'], 0) }}</p>
        </div>

        <!-- Users -->
        <div class="bg-white dark:bg-[#0f0f1a] rounded-2xl border border-gray-100 dark:border-white/5 p-5 group hover:border-blue-200 dark:hover:border-blue-900/50 transition-colors">
            <div class="flex items-center justify-between mb-3">
                <div class="w-9 h-9 bg-blue-50 dark:bg-blue-900/30 rounded-xl flex items-center justify-center">
                    <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                    </svg>
                </div>
            </div>
            <p class="text-3xl font-bold text-gray-900 dark:text-white">{{ $stats['total_users'] }}</p>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Platform Users</p>
            <p class="text-xs text-gray-400 dark:text-gray-500 mt-0.5">Across all organizations</p>
        </div>

        <!-- Subscriptions -->
        <div class="bg-white dark:bg-[#0f0f1a] rounded-2xl border border-gray-100 dark:border-white/5 p-5 group hover:border-amber-200 dark:hover:border-amber-900/50 transition-colors">
            <div class="flex items-center justify-between mb-3">
                <div class="w-9 h-9 bg-amber-50 dark:bg-amber-900/30 rounded-xl flex items-center justify-center">
                    <svg class="w-5 h-5 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                    </svg>
                </div>
                @if($stats['trial_subs'] > 0)
                    <span class="text-xs font-medium text-amber-600 dark:text-amber-400 bg-amber-50 dark:bg-amber-900/20 px-2 py-0.5 rounded-full">{{ $stats['trial_subs'] }} trials</span>
                @endif
            </div>
            <p class="text-3xl font-bold text-gray-900 dark:text-white">{{ $stats['total_subs'] }}</p>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Subscriptions</p>
            <p class="text-xs text-gray-400 dark:text-gray-500 mt-0.5">{{ $stats['active_subs'] }} paid · {{ $stats['cancelled_subs'] }} cancelled</p>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">
        <!-- MRR Chart -->
        <div class="bg-white dark:bg-[#0f0f1a] rounded-2xl border border-gray-100 dark:border-white/5 p-5">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h2 class="font-semibold text-gray-900 dark:text-white text-sm">Monthly Recurring Revenue</h2>
                    <p class="text-xs text-gray-400 mt-0.5">Last 6 months</p>
                </div>
                <div class="w-8 h-8 bg-emerald-50 dark:bg-emerald-900/20 rounded-xl flex items-center justify-center">
                    <svg class="w-4 h-4 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
                </div>
            </div>
            <canvas id="mrrChart" height="110"></canvas>
        </div>

        <!-- Org Growth Chart -->
        <div class="bg-white dark:bg-[#0f0f1a] rounded-2xl border border-gray-100 dark:border-white/5 p-5">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h2 class="font-semibold text-gray-900 dark:text-white text-sm">New Organizations</h2>
                    <p class="text-xs text-gray-400 mt-0.5">Last 6 months</p>
                </div>
                <div class="w-8 h-8 bg-indigo-50 dark:bg-indigo-900/20 rounded-xl flex items-center justify-center">
                    <svg class="w-4 h-4 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16"/></svg>
                </div>
            </div>
            <canvas id="orgChart" height="110"></canvas>
        </div>
    </div>

    <!-- Bottom Row: Recent Orgs + Plan Distribution + Quick Actions -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">
        <!-- Recent Organizations -->
        <div class="lg:col-span-2 bg-white dark:bg-[#0f0f1a] rounded-2xl border border-gray-100 dark:border-white/5 overflow-hidden">
            <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100 dark:border-white/5">
                <h2 class="font-semibold text-gray-900 dark:text-white text-sm">Recent Organizations</h2>
                <a href="/superadmin/organizations" class="text-xs font-medium text-indigo-500 hover:text-indigo-600 transition-colors">View all →</a>
            </div>
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-gray-50 dark:border-white/3">
                        <th class="px-5 py-2.5 text-left text-xs font-semibold text-gray-400 uppercase tracking-wide">Organization</th>
                        <th class="px-5 py-2.5 text-left text-xs font-semibold text-gray-400 uppercase tracking-wide">Plan</th>
                        <th class="px-5 py-2.5 text-left text-xs font-semibold text-gray-400 uppercase tracking-wide">Status</th>
                        <th class="px-5 py-2.5 text-left text-xs font-semibold text-gray-400 uppercase tracking-wide">Joined</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50 dark:divide-white/3">
                    @forelse($recentOrgs as $org)
                        @php
                            $sub    = collect($subscriptions)->firstWhere('company_id', $org->id);
                            $plan   = $sub ? collect($plans)->firstWhere('id', $sub->plan_id) : null;
                            $status = $org->suspended_at ? 'suspended' : ($org->is_active ? ($sub?->status ?? 'active') : 'inactive');
                            $statusColor = match($status) {
                                'active'    => 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400',
                                'trial'     => 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400',
                                'suspended' => 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400',
                                default     => 'bg-gray-100 text-gray-600 dark:bg-white/5 dark:text-gray-400',
                            };
                        @endphp
                        <tr class="hover:bg-gray-50 dark:hover:bg-white/2 transition-colors">
                            <td class="px-5 py-3">
                                <div class="flex items-center gap-2.5">
                                    <div class="w-7 h-7 rounded-lg bg-indigo-50 dark:bg-indigo-900/20 flex items-center justify-center text-indigo-600 dark:text-indigo-400 text-xs font-bold flex-shrink-0">
                                        {{ strtoupper(substr($org->name, 0, 2)) }}
                                    </div>
                                    <div>
                                        <p class="font-medium text-gray-900 dark:text-white text-sm">{{ $org->name }}</p>
                                        @if($org->subdomain)<p class="text-xs text-gray-400 font-mono">{{ $org->subdomain }}</p>@endif
                                    </div>
                                </div>
                            </td>
                            <td class="px-5 py-3 text-gray-500 dark:text-gray-400 text-xs">{{ $plan?->name ?? '—' }}</td>
                            <td class="px-5 py-3">
                                <span class="inline-flex px-2 py-0.5 rounded-full text-xs font-medium capitalize {{ $statusColor }}">{{ $status }}</span>
                            </td>
                            <td class="px-5 py-3 text-gray-400 text-xs">{{ \Carbon\Carbon::parse($org->created_at)->format('M d, Y') }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="px-5 py-10 text-center text-gray-400">No organizations yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Side panel -->
        <div class="space-y-4">
            <!-- Plan Distribution Donut -->
            <div class="bg-white dark:bg-[#0f0f1a] rounded-2xl border border-gray-100 dark:border-white/5 p-5">
                <h2 class="font-semibold text-gray-900 dark:text-white text-sm mb-4">Plan Distribution</h2>
                @if($planDistribution->isNotEmpty())
                    <div class="flex justify-center mb-3">
                        <canvas id="planPieChart" width="140" height="140"></canvas>
                    </div>
                @endif
                <div class="space-y-2.5">
                    @foreach($planDistribution as $i => $pd)
                        @php $pct = $stats['total_subs'] > 0 ? round($pd->count / $stats['total_subs'] * 100) : 0; @endphp
                        <div>
                            <div class="flex items-center justify-between text-xs mb-1">
                                <span class="font-medium text-gray-700 dark:text-gray-300">{{ $pd->plan_name ?? 'Unknown' }}</span>
                                <span class="text-gray-400">{{ $pd->count }} ({{ $pct }}%)</span>
                            </div>
                            <div class="h-1.5 bg-gray-100 dark:bg-white/5 rounded-full overflow-hidden">
                                @php $colors = ['bg-indigo-500','bg-emerald-500','bg-blue-500','bg-amber-500','bg-rose-500']; @endphp
                                <div class="h-full rounded-full {{ $colors[$i % count($colors)] }}" style="width:{{ $pct }}%"></div>
                            </div>
                        </div>
                    @endforeach
                    @if($planDistribution->isEmpty())
                        <p class="text-xs text-gray-400 text-center py-2">No subscriptions yet.</p>
                    @endif
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="bg-white dark:bg-[#0f0f1a] rounded-2xl border border-gray-100 dark:border-white/5 p-5">
                <h2 class="font-semibold text-gray-900 dark:text-white text-sm mb-3">Quick Actions</h2>
                <div class="space-y-1.5">
                    @foreach([
                        ['/superadmin/organizations','Manage Organizations','M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1'],
                        ['/superadmin/subscriptions','View Subscriptions','M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z'],
                        ['/superadmin/plans','Manage Plans','M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2'],
                        ['/superadmin/users','Platform Users','M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197'],
                        ['/superadmin/analytics','Analytics','M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z'],
                    ] as [$href, $label, $path])
                    <a href="{{ $href }}" class="flex items-center gap-2.5 px-3 py-2.5 rounded-xl bg-gray-50 dark:bg-white/3 hover:bg-indigo-50 dark:hover:bg-indigo-900/20 text-gray-600 dark:text-gray-300 hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors text-sm font-medium">
                        <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $path }}"/></svg>
                        {{ $label }}
                    </a>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    <!-- Trials expiring -->
    @if($trialsExpiring->count())
    <div class="bg-amber-50 dark:bg-amber-900/10 border border-amber-200 dark:border-amber-800/50 rounded-2xl p-5">
        <div class="flex items-center gap-2 mb-3">
            <svg class="w-5 h-5 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
            </svg>
            <h3 class="font-semibold text-amber-800 dark:text-amber-200 text-sm">{{ $trialsExpiring->count() }} Trial{{ $trialsExpiring->count() > 1 ? 's' : '' }} Expiring Within 7 Days</h3>
        </div>
        <div class="flex flex-wrap gap-2">
            @foreach($trialsExpiring as $org)
                @php $daysLeft = \Carbon\Carbon::now()->diffInDays(\Carbon\Carbon::parse($org->trial_ends_at), false); @endphp
                <a href="/superadmin/organizations/{{ $org->id }}"
                   class="flex items-center gap-2 px-3 py-1.5 bg-white dark:bg-amber-900/20 border border-amber-200 dark:border-amber-700/50 rounded-xl text-sm hover:border-amber-400 transition-colors">
                    <span class="font-medium text-amber-800 dark:text-amber-200">{{ $org->name }}</span>
                    <span class="text-xs text-amber-600 dark:text-amber-400">{{ $daysLeft }}d left</span>
                </a>
            @endforeach
        </div>
    </div>
    @endif

    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <script>
        const isDark = document.documentElement.classList.contains('dark');
        const gridColor  = isDark ? 'rgba(255,255,255,0.05)' : 'rgba(0,0,0,0.06)';
        const labelColor = isDark ? '#9ca3af' : '#6b7280';

        const baseOpts = (yLabel) => ({
            responsive: true,
            plugins: { legend: { display: false } },
            scales: {
                x: { grid: { color: gridColor }, ticks: { color: labelColor, font: { size: 11 } } },
                y: {
                    grid: { color: gridColor },
                    ticks: { color: labelColor, font: { size: 11 },
                             callback: v => yLabel === '$' ? '$'+v.toLocaleString() : v }
                }
            }
        });

        // MRR chart
        new Chart(document.getElementById('mrrChart'), {
            type: 'line',
            data: {
                labels: @json($mrrGrowth['labels']),
                datasets: [{
                    data: @json($mrrGrowth['values']),
                    borderColor: '#10b981',
                    backgroundColor: 'rgba(16,185,129,0.08)',
                    borderWidth: 2,
                    pointRadius: 4,
                    pointBackgroundColor: '#10b981',
                    fill: true,
                    tension: 0.4
                }]
            },
            options: baseOpts('$')
        });

        // Org growth chart
        new Chart(document.getElementById('orgChart'), {
            type: 'bar',
            data: {
                labels: @json($orgGrowth['labels']),
                datasets: [{
                    data: @json($orgGrowth['values']),
                    backgroundColor: 'rgba(99,102,241,0.7)',
                    borderRadius: 6,
                    borderSkipped: false,
                }]
            },
            options: baseOpts('')
        });

        // Plan distribution donut
        @if($planDistribution->isNotEmpty())
        new Chart(document.getElementById('planPieChart'), {
            type: 'doughnut',
            data: {
                labels: @json($planDistribution->pluck('plan_name')),
                datasets: [{
                    data: @json($planDistribution->pluck('count')),
                    backgroundColor: ['#6366f1','#10b981','#3b82f6','#f59e0b','#ef4444'],
                    borderWidth: 0,
                    hoverOffset: 4
                }]
            },
            options: {
                responsive: false,
                cutout: '65%',
                plugins: { legend: { display: false } }
            }
        });
        @endif
    </script>
@endsection

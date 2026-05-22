@extends('app.layouts.app')

@section('title', 'Projects')
@section('breadcrumb', 'Projects')

@section('head_scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.2/dist/chart.umd.min.js"></script>
@endsection

@section('topbar_actions')
@if(!$hasRealData)
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
        <h1 class="text-xl font-bold text-gray-900 dark:text-white">Projects</h1>
        <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">{{ date('l, F j, Y') }} · Project health & task overview</p>
    </div>
    <div class="flex gap-2">
        <a href="/admin/projects/projects" class="flex items-center gap-2 text-sm font-medium text-gray-600 dark:text-gray-300 px-4 py-2 rounded-xl border border-gray-200 dark:border-white/10 hover:bg-gray-50 dark:hover:bg-white/5 transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/></svg>
            All Projects
        </a>
        <a href="/admin/projects/projects/create" class="flex items-center gap-2 bg-brand-600 hover:bg-brand-700 text-white text-sm font-medium px-4 py-2 rounded-xl transition-colors shadow-sm">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            New Project
        </a>
    </div>
</div>

<!-- ── Stat Cards ── -->
<div class="grid grid-cols-2 lg:grid-cols-5 gap-4">
    @php
    $pStats = [
        ['label'=>'Total Projects', 'value'=>$totalProjects, 'color'=>'brand',   'change'=>null,
         'icon'=>'<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>'],
        ['label'=>'Total Tasks',    'value'=>$totalTasks,    'color'=>'blue',    'change'=>null,
         'icon'=>'<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>'],
        ['label'=>'Open Tasks',     'value'=>$openTasks,     'color'=>'orange',  'change'=>null,
         'icon'=>'<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>'],
        ['label'=>'Completed',      'value'=>$doneTasks,     'color'=>'emerald', 'change'=>null,
         'icon'=>'<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M5 13l4 4L19 7"/>'],
        ['label'=>'Overdue',        'value'=>$overdueTasks,  'color'=>'red',     'change'=>null,
         'icon'=>'<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>'],
    ];
    $pColors = [
        'brand'   => ['bg'=>'bg-brand-50 dark:bg-brand-900/20',   'icon'=>'text-brand-600 dark:text-brand-400'],
        'blue'    => ['bg'=>'bg-blue-50 dark:bg-blue-900/20',     'icon'=>'text-blue-600 dark:text-blue-400'],
        'orange'  => ['bg'=>'bg-orange-50 dark:bg-orange-900/20', 'icon'=>'text-orange-600 dark:text-orange-400'],
        'emerald' => ['bg'=>'bg-emerald-50 dark:bg-emerald-900/20','icon'=>'text-emerald-600 dark:text-emerald-400'],
        'red'     => ['bg'=>'bg-red-50 dark:bg-red-900/20',       'icon'=>'text-red-500 dark:text-red-400'],
    ];
    @endphp
    @foreach($pStats as $s)
    @php $c = $pColors[$s['color']]; @endphp
    <div class="stat-card bg-white dark:bg-[#16161e] rounded-2xl border border-gray-100 dark:border-white/6 p-5 shadow-sm">
        <div class="flex items-start justify-between mb-3">
            <div class="{{ $c['bg'] }} rounded-xl p-2.5">
                <svg class="w-5 h-5 {{ $c['icon'] }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">{!! $s['icon'] !!}</svg>
            </div>
        </div>
        <div class="text-2xl font-bold text-gray-900 dark:text-white mb-0.5">{{ $s['value'] }}</div>
        <div class="text-xs text-gray-500 dark:text-gray-400">{{ $s['label'] }}</div>
    </div>
    @endforeach
</div>

<!-- ── Charts Row 1 ── -->
<div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
    <!-- Tasks by Stage bar -->
    <div class="lg:col-span-2 bg-white dark:bg-[#16161e] rounded-2xl border border-gray-100 dark:border-white/6 p-5 shadow-sm">
        <div class="mb-4">
            <h3 class="font-semibold text-gray-900 dark:text-white text-sm">Tasks by Stage</h3>
            <p class="text-xs text-gray-400 mt-0.5">Workload distribution across stages</p>
        </div>
        <div class="relative" style="height:220px;">
            <canvas id="stageBarChart"></canvas>
        </div>
    </div>

    <!-- Task Status Donut -->
    <div class="bg-white dark:bg-[#16161e] rounded-2xl border border-gray-100 dark:border-white/6 p-5 shadow-sm flex flex-col">
        <div class="mb-4">
            <h3 class="font-semibold text-gray-900 dark:text-white text-sm">Task Status</h3>
            <p class="text-xs text-gray-400 mt-0.5">Current state breakdown</p>
        </div>
        <div class="flex-1 flex items-center justify-center" style="min-height:140px;">
            <canvas id="statusDonut"></canvas>
        </div>
        <div class="mt-4 space-y-2">
            @php
            $stateLabels = ['in_progress'=>'In Progress','1_done'=>'Done','changes_requested'=>'Changes Req.','approved'=>'Approved','cancelled'=>'Cancelled'];
            $stateColors = ['in_progress'=>'#60a5fa','1_done'=>'#34d399','changes_requested'=>'#f59e0b','approved'=>'#7c3aed','cancelled'=>'#f87171'];
            @endphp
            @foreach($tasksByState->take(5) as $state => $cnt)
            <div class="flex items-center justify-between text-xs">
                <div class="flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full flex-shrink-0" style="background:{{ $stateColors[$state] ?? '#94a3b8' }}"></span>
                    <span class="text-gray-600 dark:text-gray-400">{{ $stateLabels[$state] ?? ucfirst(str_replace('_',' ',$state)) }}</span>
                </div>
                <span class="font-semibold text-gray-800 dark:text-gray-200">{{ $cnt }}</span>
            </div>
            @endforeach
        </div>
    </div>
</div>

<!-- ── Charts Row 2: Monthly tasks + Projects progress ── -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
    <!-- Monthly tasks created area -->
    <div class="bg-white dark:bg-[#16161e] rounded-2xl border border-gray-100 dark:border-white/6 p-5 shadow-sm">
        <div class="mb-4">
            <h3 class="font-semibold text-gray-900 dark:text-white text-sm">Tasks Created per Month</h3>
            <p class="text-xs text-gray-400 mt-0.5">{{ date('Y') }}</p>
        </div>
        <div class="relative" style="height:200px;">
            <canvas id="monthlyTasksChart"></canvas>
        </div>
    </div>

    <!-- Project Progress list -->
    <div class="bg-white dark:bg-[#16161e] rounded-2xl border border-gray-100 dark:border-white/6 p-5 shadow-sm">
        <div class="flex items-center justify-between mb-4">
            <div>
                <h3 class="font-semibold text-gray-900 dark:text-white text-sm">Project Progress</h3>
                <p class="text-xs text-gray-400 mt-0.5">Completion rate per project</p>
            </div>
            <a href="/admin/projects/projects" class="text-xs text-brand-600 dark:text-brand-400 font-medium hover:underline">View all →</a>
        </div>
        <div class="space-y-4">
            @php
            $projectColors = ['purple'=>'#7c3aed','blue'=>'#3b82f6','green'=>'#10b981','orange'=>'#f59e0b','cyan'=>'#06b6d4','red'=>'#ef4444','pink'=>'#ec4899'];
            @endphp
            @foreach($projects->take(5) as $proj)
            @php $color = $projectColors[$proj->color ?? 'purple'] ?? '#7c3aed'; @endphp
            <div>
                <div class="flex items-center justify-between mb-1.5">
                    <div class="flex items-center gap-2 min-w-0">
                        <span class="w-2 h-2 rounded-full flex-shrink-0" style="background:{{ $color }}"></span>
                        <span class="text-xs font-medium text-gray-800 dark:text-gray-200 truncate">{{ $proj->name }}</span>
                    </div>
                    <div class="flex items-center gap-2 flex-shrink-0">
                        <span class="text-[11px] text-gray-400">{{ $proj->done_tasks }}/{{ $proj->total_tasks }}</span>
                        <span class="text-xs font-bold text-gray-700 dark:text-gray-300 w-8 text-right">{{ $proj->progress }}%</span>
                    </div>
                </div>
                <div class="h-1.5 bg-gray-100 dark:bg-white/5 rounded-full overflow-hidden">
                    <div class="h-full rounded-full transition-all" style="width:{{ $proj->progress }}%;background:{{ $color }}"></div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</div>

<!-- ── Recent Tasks Table ── -->
<div class="bg-white dark:bg-[#16161e] rounded-2xl border border-gray-100 dark:border-white/6 shadow-sm overflow-hidden">
    <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100 dark:border-white/5">
        <div>
            <h3 class="font-semibold text-gray-900 dark:text-white text-sm">Recent Tasks</h3>
            <p class="text-xs text-gray-400 mt-0.5">Latest created & updated tasks</p>
        </div>
        <a href="/admin/projects/tasks" class="text-xs text-brand-600 dark:text-brand-400 font-medium hover:underline">View all →</a>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="text-left text-[11px] font-semibold text-gray-400 uppercase tracking-wider border-b border-gray-50 dark:border-white/4">
                    <th class="px-5 py-3">Task</th>
                    <th class="px-5 py-3">Project</th>
                    <th class="px-5 py-3">Stage</th>
                    <th class="px-5 py-3">Priority</th>
                    <th class="px-5 py-3">Progress</th>
                    <th class="px-5 py-3">Deadline</th>
                    <th class="px-5 py-3">Status</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50 dark:divide-white/4">
                @php
                $taskStateBadge = [
                    'in_progress'        => 'bg-blue-50 dark:bg-blue-900/20 text-blue-600 dark:text-blue-400',
                    '1_done'             => 'badge-up',
                    'changes_requested'  => 'bg-amber-50 dark:bg-amber-900/20 text-amber-600 dark:text-amber-400',
                    'approved'           => 'bg-brand-50 dark:bg-brand-900/20 text-brand-700 dark:text-brand-400',
                    'cancelled'          => 'badge-down',
                ];
                @endphp
                @forelse($recentTasks as $task)
                <tr class="hover:bg-gray-50 dark:hover:bg-white/3 transition-colors">
                    <td class="px-5 py-3.5">
                        <div class="flex items-center gap-2">
                            @if($task->priority == '1')
                            <svg class="w-3.5 h-3.5 text-orange-400 flex-shrink-0" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2l2.4 7.4H22l-6.2 4.5 2.4 7.4L12 17l-6.2 4.3 2.4-7.4L2 9.4h7.6z"/></svg>
                            @endif
                            <span class="font-medium text-gray-800 dark:text-gray-200 text-xs">{{ $task->title }}</span>
                        </div>
                    </td>
                    <td class="px-5 py-3.5 text-xs text-gray-500 dark:text-gray-400">{{ $task->project_name ?? '—' }}</td>
                    <td class="px-5 py-3.5 text-xs text-gray-500 dark:text-gray-400">{{ $task->stage_name ?? '—' }}</td>
                    <td class="px-5 py-3.5">
                        <span class="badge {{ $task->priority == '1' ? 'bg-orange-50 dark:bg-orange-900/20 text-orange-600 dark:text-orange-400' : 'badge-neutral' }} text-[10px]">
                            {{ $task->priority == '1' ? 'High' : 'Normal' }}
                        </span>
                    </td>
                    <td class="px-5 py-3.5">
                        <div class="flex items-center gap-2">
                            <div class="w-16 h-1 bg-gray-100 dark:bg-white/5 rounded-full overflow-hidden">
                                <div class="h-full bg-brand-500 rounded-full" style="width:{{ $task->progress ?? 0 }}%"></div>
                            </div>
                            <span class="text-[11px] text-gray-500 dark:text-gray-400">{{ $task->progress ?? 0 }}%</span>
                        </div>
                    </td>
                    <td class="px-5 py-3.5 text-xs text-gray-500 dark:text-gray-400">
                        @if($task->deadline)
                            @php $isOverdue = strtotime($task->deadline) < time() && !in_array($task->state, ['1_done','cancelled']); @endphp
                            <span class="{{ $isOverdue ? 'text-red-500' : '' }}">{{ date('M d', strtotime($task->deadline)) }}</span>
                        @else
                            —
                        @endif
                    </td>
                    <td class="px-5 py-3.5">
                        <span class="badge {{ $taskStateBadge[$task->state ?? ''] ?? 'badge-neutral' }} text-[10px]">
                            {{ $stateLabels[$task->state ?? ''] ?? ucfirst(str_replace('_',' ', $task->state ?? 'open')) }}
                        </span>
                    </td>
                </tr>
                @empty
                <tr><td colspan="7" class="px-5 py-8 text-center text-xs text-gray-400">
                    No tasks yet · <a href="/admin/projects/tasks/create" class="text-brand-600 hover:underline">Create one</a>
                </td></tr>
                @endforelse
            </tbody>
        </table>
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
    bodyColor: isDark() ? '#9ca3af' : '#6b7280',
    borderColor: isDark() ? 'rgba(255,255,255,0.08)' : 'rgba(0,0,0,0.08)',
    borderWidth: 1, padding: 10,
});

const palette   = ['#7c3aed','#60a5fa','#34d399','#f59e0b','#f87171','#a78bfa','#2dd4bf','#fb923c'];
const months    = @json($months->values());
const stageLabels = @json($tasksByStage->keys());
const stageValues = @json($tasksByStage->values());
const stateLabels = @json($tasksByState->keys());
const stateValues = @json($tasksByState->values());
const monthlyTasksData = @json($tasksCreatedData->values());

Chart.defaults.font.family = 'Inter, sans-serif';
Chart.defaults.font.size   = 11;

// 1. Stage Bar
new Chart(document.getElementById('stageBarChart'), {
    type: 'bar',
    data: { labels: stageLabels, datasets: [{
        label: 'Tasks', data: stageValues,
        backgroundColor: stageLabels.map((_, i) => palette[i % palette.length]),
        borderRadius: 8, borderSkipped: false,
    }]},
    options: {
        responsive: true, maintainAspectRatio: false,
        plugins: { legend: { display: false }, tooltip: tip() },
        scales: {
            x: { grid: { display: false }, ticks: { color: lblColor() } },
            y: { grid: { color: gridColor() }, ticks: { color: lblColor(), precision: 0 } }
        }
    }
});

// 2. Status Donut
new Chart(document.getElementById('statusDonut'), {
    type: 'doughnut',
    data: { labels: stateLabels, datasets: [{ data: stateValues,
        backgroundColor: ['#60a5fa','#34d399','#f59e0b','#7c3aed','#f87171'],
        borderWidth: 0, hoverOffset: 6 }] },
    options: {
        responsive: true, maintainAspectRatio: false, cutout: '72%',
        plugins: { legend: { display: false }, tooltip: tip() }
    }
});

// 3. Monthly Tasks Area
const mCtx = document.getElementById('monthlyTasksChart').getContext('2d');
const mGrad = mCtx.createLinearGradient(0, 0, 0, 200);
mGrad.addColorStop(0, 'rgba(124,58,237,0.2)');
mGrad.addColorStop(1, 'rgba(124,58,237,0)');
new Chart(mCtx, {
    type: 'line',
    data: { labels: months, datasets: [{
        label: 'Tasks Created', data: monthlyTasksData,
        borderColor: '#7c3aed', backgroundColor: mGrad,
        borderWidth: 2.5, pointRadius: 4, pointBackgroundColor: '#7c3aed',
        pointBorderColor: '#fff', pointBorderWidth: 2, tension: 0.4, fill: true,
    }]},
    options: {
        responsive: true, maintainAspectRatio: false,
        plugins: { legend: { display: false }, tooltip: tip() },
        scales: {
            x: { grid: { display: false }, ticks: { color: lblColor() } },
            y: { grid: { color: gridColor() }, ticks: { color: lblColor(), precision: 0 } }
        }
    }
});
</script>
@endsection

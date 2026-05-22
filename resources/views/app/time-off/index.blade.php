@extends('app.layouts.app')
@section('title', 'Time Off')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Time Off</h1>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Leave requests and approvals</p>
        </div>
        <a href="/app/time-off/create" class="inline-flex items-center gap-2 px-4 py-2 bg-brand-600 hover:bg-brand-700 text-white rounded-lg text-sm font-medium shadow-sm transition-colors">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            New Request
        </a>
    </div>

    {{-- Stats --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        @foreach([
            ['label'=>'Total','value'=>$stats['total'],'color'=>'brand'],
            ['label'=>'Draft','value'=>$stats['draft'],'color'=>'gray'],
            ['label'=>'Pending','value'=>$stats['pending'],'color'=>'yellow'],
            ['label'=>'Approved','value'=>$stats['approved'],'color'=>'green'],
        ] as $s)
        <div class="bg-white dark:bg-gray-800 rounded-xl p-4 border border-gray-200 dark:border-gray-700 shadow-sm">
            <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide">{{ $s['label'] }}</p>
            <p class="text-2xl font-bold text-gray-900 dark:text-white mt-1">{{ $s['value'] }}</p>
        </div>
        @endforeach
    </div>

    {{-- Filter tabs --}}
    <div class="flex gap-2 border-b border-gray-200 dark:border-gray-700">
        @foreach(['all'=>'All','draft'=>'Draft','confirm'=>'Pending','validate'=>'Approved','refuse'=>'Refused'] as $val=>$label)
        <a href="/app/time-off?{{ http_build_query(['filter'=>$val,'search'=>$search]) }}"
           class="px-4 py-2 text-sm font-medium whitespace-nowrap {{ $filter==$val ? 'text-brand-600 border-b-2 border-brand-600' : 'text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200' }}">{{ $label }}</a>
        @endforeach
    </div>

    {{-- Search --}}
    <form method="GET" action="/app/time-off" class="flex gap-3">
        <input type="hidden" name="filter" value="{{ $filter }}">
        <input type="text" name="search" value="{{ $search }}" placeholder="Search employees…"
            class="flex-1 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-white px-4 py-2 text-sm focus:ring-2 focus:ring-brand-500 focus:border-transparent">
        <button type="submit" class="px-4 py-2 bg-brand-600 hover:bg-brand-700 text-white rounded-lg text-sm font-medium">Search</button>
    </form>

    {{-- Table --}}
    <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 dark:bg-gray-700/50">
                <tr>
                    <th class="px-4 py-3 text-left font-semibold text-gray-600 dark:text-gray-300">Employee</th>
                    <th class="px-4 py-3 text-left font-semibold text-gray-600 dark:text-gray-300">Leave Type</th>
                    <th class="px-4 py-3 text-left font-semibold text-gray-600 dark:text-gray-300">From</th>
                    <th class="px-4 py-3 text-left font-semibold text-gray-600 dark:text-gray-300">To</th>
                    <th class="px-4 py-3 text-left font-semibold text-gray-600 dark:text-gray-300">Days</th>
                    <th class="px-4 py-3 text-left font-semibold text-gray-600 dark:text-gray-300">State</th>
                    <th class="px-4 py-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                @forelse($leaves as $leave)
                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/30 transition-colors">
                    <td class="px-4 py-3 font-medium text-gray-900 dark:text-white">{{ $leave->employee_name ?? '—' }}</td>
                    <td class="px-4 py-3 text-gray-600 dark:text-gray-300">{{ $leave->leave_type_name ?? '—' }}</td>
                    <td class="px-4 py-3 text-gray-600 dark:text-gray-300">{{ $leave->date_from ? date('d M Y', strtotime($leave->date_from)) : '—' }}</td>
                    <td class="px-4 py-3 text-gray-600 dark:text-gray-300">{{ $leave->date_to ? date('d M Y', strtotime($leave->date_to)) : '—' }}</td>
                    <td class="px-4 py-3 text-gray-600 dark:text-gray-300">{{ $leave->number_of_days ?? '—' }}</td>
                    <td class="px-4 py-3">
                        @php $stateColors=['draft'=>'gray','confirm'=>'yellow','validate1'=>'yellow','validate'=>'green','refuse'=>'red']; $sc=$stateColors[$leave->state]??'gray'; @endphp
                        <span class="px-2 py-0.5 rounded-full text-xs font-medium bg-{{ $sc }}-100 text-{{ $sc }}-700 dark:bg-{{ $sc }}-900/30 dark:text-{{ $sc }}-400 capitalize">{{ ['draft'=>'Draft','confirm'=>'Pending','validate1'=>'To Approve','validate'=>'Approved','refuse'=>'Refused'][$leave->state]??$leave->state }}</span>
                    </td>
                    <td class="px-4 py-3 text-right">
                        <a href="/app/time-off/{{ $leave->id }}" class="text-brand-600 hover:text-brand-700 font-medium text-xs">View</a>
                    </td>
                </tr>
                @empty
                <tr><td colspan="7" class="px-4 py-10 text-center text-gray-500 dark:text-gray-400">No leave requests found.</td></tr>
                @endforelse
            </tbody>
        </table>
        @if($leaves->hasPages())
        <div class="px-4 py-3 border-t border-gray-100 dark:border-gray-700">{{ $leaves->links() }}</div>
        @endif
    </div>
</div>
@endsection

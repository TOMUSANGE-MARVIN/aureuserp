@extends('app.layouts.app')
@section('title', 'Recruitment')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Recruitment</h1>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Manage job applicants and candidates</p>
        </div>
        <a href="/app/recruitment/create" class="inline-flex items-center gap-2 px-4 py-2 bg-brand-600 hover:bg-brand-700 text-white rounded-lg text-sm font-medium shadow-sm transition-colors">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            New Application
        </a>
    </div>

    {{-- Stats --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        @foreach([
            ['label'=>'Total','value'=>$stats['total'],'color'=>'brand'],
            ['label'=>'New','value'=>$stats['new'],'color'=>'blue'],
            ['label'=>'In Progress','value'=>$stats['in_progress'],'color'=>'yellow'],
            ['label'=>'Hired','value'=>$stats['done'],'color'=>'green'],
        ] as $s)
        <div class="bg-white dark:bg-gray-800 rounded-xl p-4 border border-gray-200 dark:border-gray-700 shadow-sm">
            <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide">{{ $s['label'] }}</p>
            <p class="text-2xl font-bold text-gray-900 dark:text-white mt-1">{{ $s['value'] }}</p>
        </div>
        @endforeach
    </div>

    {{-- Stage filter tabs --}}
    <div class="flex gap-2 overflow-x-auto border-b border-gray-200 dark:border-gray-700">
        <a href="/app/recruitment?{{ http_build_query(['search'=>$search,'stage'=>'all']) }}"
           class="px-4 py-2 text-sm font-medium whitespace-nowrap {{ $stageFilter=='all' ? 'text-brand-600 border-b-2 border-brand-600' : 'text-gray-500 dark:text-gray-400 hover:text-gray-700' }}">All</a>
        @foreach($stages as $stage)
        <a href="/app/recruitment?{{ http_build_query(['search'=>$search,'stage'=>$stage->id]) }}"
           class="px-4 py-2 text-sm font-medium whitespace-nowrap {{ $stageFilter==$stage->id ? 'text-brand-600 border-b-2 border-brand-600' : 'text-gray-500 dark:text-gray-400 hover:text-gray-700' }}">{{ $stage->name }}</a>
        @endforeach
    </div>

    {{-- Search --}}
    <form method="GET" action="/app/recruitment" class="flex gap-3">
        <input type="hidden" name="stage" value="{{ $stageFilter }}">
        <input type="text" name="search" value="{{ $search }}" placeholder="Search candidates…"
            class="flex-1 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-white px-4 py-2 text-sm focus:ring-2 focus:ring-brand-500 focus:border-transparent">
        <button type="submit" class="px-4 py-2 bg-brand-600 hover:bg-brand-700 text-white rounded-lg text-sm font-medium">Search</button>
    </form>

    {{-- Table --}}
    <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 dark:bg-gray-700/50">
                <tr>
                    <th class="px-4 py-3 text-left font-semibold text-gray-600 dark:text-gray-300">Candidate</th>
                    <th class="px-4 py-3 text-left font-semibold text-gray-600 dark:text-gray-300">Stage</th>
                    <th class="px-4 py-3 text-left font-semibold text-gray-600 dark:text-gray-300">Department</th>
                    <th class="px-4 py-3 text-left font-semibold text-gray-600 dark:text-gray-300">Priority</th>
                    <th class="px-4 py-3 text-left font-semibold text-gray-600 dark:text-gray-300">Status</th>
                    <th class="px-4 py-3 text-left font-semibold text-gray-600 dark:text-gray-300">Applied</th>
                    <th class="px-4 py-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                @forelse($applicants as $a)
                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/30 transition-colors">
                    <td class="px-4 py-3">
                        <div class="font-medium text-gray-900 dark:text-white">{{ $a->candidate_name ?? 'Unknown' }}</div>
                        <div class="text-xs text-gray-500 dark:text-gray-400">{{ $a->candidate_email ?? '' }}</div>
                    </td>
                    <td class="px-4 py-3 text-gray-600 dark:text-gray-300">{{ $a->stage_name ?? '—' }}</td>
                    <td class="px-4 py-3 text-gray-600 dark:text-gray-300">{{ $a->department_name ?? '—' }}</td>
                    <td class="px-4 py-3">@for($i=0;$i<($a->priority??0);$i++)<span class="text-yellow-400">★</span>@endfor</td>
                    <td class="px-4 py-3">
                        @php $stateColors=['new'=>'blue','in_progress'=>'yellow','blocked'=>'red','ready'=>'green','done'=>'brand']; $sc=$stateColors[$a->state]??'gray'; @endphp
                        <span class="px-2 py-0.5 rounded-full text-xs font-medium bg-{{ $sc }}-100 text-{{ $sc }}-700 dark:bg-{{ $sc }}-900/30 dark:text-{{ $sc }}-400 capitalize">{{ str_replace('_',' ',$a->state) }}</span>
                    </td>
                    <td class="px-4 py-3 text-gray-500 dark:text-gray-400 text-xs">{{ $a->date_opened ? date('d M Y', strtotime($a->date_opened)) : ($a->created_at ? date('d M Y', strtotime($a->created_at)) : '—') }}</td>
                    <td class="px-4 py-3 text-right">
                        <a href="/app/recruitment/{{ $a->id }}" class="text-brand-600 hover:text-brand-700 font-medium text-xs">View</a>
                    </td>
                </tr>
                @empty
                <tr><td colspan="7" class="px-4 py-10 text-center text-gray-500 dark:text-gray-400">No applicants found.</td></tr>
                @endforelse
            </tbody>
        </table>
        @if($applicants->hasPages())
        <div class="px-4 py-3 border-t border-gray-100 dark:border-gray-700">{{ $applicants->links() }}</div>
        @endif
    </div>
</div>
@endsection

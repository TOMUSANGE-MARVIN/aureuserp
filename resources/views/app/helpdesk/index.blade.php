@extends('app.layouts.app')
@section('title', 'Helpdesk')
@section('breadcrumb', 'Helpdesk')

@section('content')
@if(session('success'))
<div class="bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-xl p-4 text-sm text-green-800 dark:text-green-300">{{ session('success') }}</div>
@endif

{{-- Header --}}
<div class="flex items-center justify-between">
    <div>
        <h1 class="text-xl font-bold text-gray-900 dark:text-white">Helpdesk</h1>
        <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">Manage customer support tickets</p>
    </div>
    <div class="flex items-center gap-2">
        <a href="{{ route('helpdesk.teams') }}"
           class="px-3 py-2 text-sm border border-gray-200 dark:border-white/10 rounded-xl text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-white/5 transition">
            Teams
        </a>
        <a href="{{ route('helpdesk.create') }}"
           class="px-4 py-2 text-sm bg-brand-600 hover:bg-brand-700 text-white rounded-xl transition font-medium">
            + New Ticket
        </a>
    </div>
</div>

{{-- Stats --}}
<div class="grid grid-cols-2 md:grid-cols-4 gap-4">
    @php
        $statCards = [
            ['label'=>'Total','value'=>$stats->total??0,'color'=>'text-gray-900 dark:text-white'],
            ['label'=>'Open','value'=>$stats->open??0,'color'=>'text-yellow-600 dark:text-yellow-400'],
            ['label'=>'In Progress','value'=>$stats->in_progress??0,'color'=>'text-blue-600 dark:text-blue-400'],
            ['label'=>'Resolved','value'=>$stats->resolved??0,'color'=>'text-green-600 dark:text-green-400'],
        ];
    @endphp
    @foreach($statCards as $card)
    <div class="bg-white dark:bg-[#111118] rounded-2xl border border-gray-100 dark:border-white/5 p-4">
        <p class="text-xs text-gray-500 dark:text-gray-400 font-medium uppercase tracking-wide">{{ $card['label'] }}</p>
        <p class="text-2xl font-bold {{ $card['color'] }} mt-1">{{ $card['value'] }}</p>
    </div>
    @endforeach
</div>

{{-- Filters --}}
<form method="GET" class="flex flex-wrap gap-3 items-end">
    <div class="flex-1 min-w-48">
        <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1">Search</label>
        <input type="text" name="search" value="{{ $search }}" placeholder="Title, customer name or email…"
               class="w-full px-3 py-2 text-sm border border-gray-200 dark:border-white/10 rounded-xl bg-white dark:bg-[#111118] text-gray-900 dark:text-white">
    </div>
    <div>
        <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1">Status</label>
        <select name="status" class="px-3 py-2 text-sm border border-gray-200 dark:border-white/10 rounded-xl bg-white dark:bg-[#111118] text-gray-900 dark:text-white">
            <option value="">All Statuses</option>
            <option value="open" {{ $status==='open'?'selected':'' }}>Open</option>
            <option value="in_progress" {{ $status==='in_progress'?'selected':'' }}>In Progress</option>
            <option value="resolved" {{ $status==='resolved'?'selected':'' }}>Resolved</option>
            <option value="closed" {{ $status==='closed'?'selected':'' }}>Closed</option>
        </select>
    </div>
    <div>
        <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1">Priority</label>
        <select name="priority" class="px-3 py-2 text-sm border border-gray-200 dark:border-white/10 rounded-xl bg-white dark:bg-[#111118] text-gray-900 dark:text-white">
            <option value="">All Priorities</option>
            <option value="urgent" {{ $priority==='urgent'?'selected':'' }}>Urgent</option>
            <option value="high" {{ $priority==='high'?'selected':'' }}>High</option>
            <option value="medium" {{ $priority==='medium'?'selected':'' }}>Medium</option>
            <option value="low" {{ $priority==='low'?'selected':'' }}>Low</option>
        </select>
    </div>
    <div>
        <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1">Team</label>
        <select name="team_id" class="px-3 py-2 text-sm border border-gray-200 dark:border-white/10 rounded-xl bg-white dark:bg-[#111118] text-gray-900 dark:text-white">
            <option value="">All Teams</option>
            @foreach($teams as $team)
                <option value="{{ $team->id }}" {{ $teamId==$team->id?'selected':'' }}>{{ $team->name }}</option>
            @endforeach
        </select>
    </div>
    <button type="submit" class="px-4 py-2 text-sm bg-brand-600 hover:bg-brand-700 text-white rounded-xl transition">Filter</button>
    @if($search||$status||$priority||$teamId)
    <a href="{{ route('helpdesk.index') }}" class="px-4 py-2 text-sm border border-gray-200 dark:border-white/10 text-gray-600 dark:text-gray-300 rounded-xl hover:bg-gray-50 dark:hover:bg-white/5 transition">Clear</a>
    @endif
</form>

{{-- Tickets Table --}}
<div class="bg-white dark:bg-[#111118] rounded-2xl border border-gray-100 dark:border-white/5 overflow-hidden">
    <table class="w-full text-sm">
        <thead>
            <tr class="border-b border-gray-100 dark:border-white/5">
                <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide">#</th>
                <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide">Title</th>
                <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide">Customer</th>
                <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide">Team</th>
                <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide">Assigned</th>
                <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide">Priority</th>
                <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide">Status</th>
                <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide">Created</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-50 dark:divide-white/[0.03]">
            @forelse($tickets as $ticket)
            <tr class="hover:bg-gray-50 dark:hover:bg-white/[0.02] cursor-pointer transition" onclick="window.location='{{ route('helpdesk.show', $ticket->id) }}'">
                <td class="px-4 py-3 text-gray-400 dark:text-gray-500 font-mono text-xs">#{{ $ticket->id }}</td>
                <td class="px-4 py-3">
                    <a href="{{ route('helpdesk.show', $ticket->id) }}" class="font-medium text-gray-900 dark:text-white hover:text-brand-600 dark:hover:text-brand-400">
                        {{ Str::limit($ticket->title, 55) }}
                    </a>
                    @if($ticket->type)<span class="ml-1 px-1.5 py-0.5 text-xs rounded bg-gray-100 dark:bg-white/5 text-gray-500 dark:text-gray-400">{{ $ticket->type }}</span>@endif
                </td>
                <td class="px-4 py-3 text-gray-600 dark:text-gray-300">
                    {{ $ticket->customer_name ?: '—' }}
                    @if($ticket->customer_email)<p class="text-xs text-gray-400">{{ $ticket->customer_email }}</p>@endif
                </td>
                <td class="px-4 py-3 text-gray-500 dark:text-gray-400">{{ $ticket->team_name ?: '—' }}</td>
                <td class="px-4 py-3 text-gray-500 dark:text-gray-400">{{ $ticket->assignee_name ?: '—' }}</td>
                <td class="px-4 py-3">
                    @php $pc = ['urgent'=>'bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-400','high'=>'bg-orange-100 dark:bg-orange-900/30 text-orange-700 dark:text-orange-400','medium'=>'bg-yellow-100 dark:bg-yellow-900/30 text-yellow-700 dark:text-yellow-400','low'=>'bg-gray-100 dark:bg-white/5 text-gray-600 dark:text-gray-400']; @endphp
                    <span class="px-2 py-0.5 text-xs rounded-full {{ $pc[$ticket->priority] ?? '' }} capitalize">{{ $ticket->priority }}</span>
                </td>
                <td class="px-4 py-3">
                    @php $sc = ['open'=>'bg-yellow-100 dark:bg-yellow-900/30 text-yellow-700 dark:text-yellow-400','in_progress'=>'bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-400','resolved'=>'bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400','closed'=>'bg-gray-100 dark:bg-white/5 text-gray-500 dark:text-gray-400']; @endphp
                    <span class="px-2 py-0.5 text-xs rounded-full {{ $sc[$ticket->status] ?? '' }} capitalize">{{ str_replace('_',' ',$ticket->status) }}</span>
                </td>
                <td class="px-4 py-3 text-xs text-gray-400 dark:text-gray-500">{{ \Carbon\Carbon::parse($ticket->created_at)->diffForHumans() }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="8" class="px-4 py-12 text-center text-gray-400 dark:text-gray-500">No tickets found.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
    @if($tickets->hasPages())
    <div class="px-4 py-3 border-t border-gray-100 dark:border-white/5">{{ $tickets->links() }}</div>
    @endif
</div>
@endsection

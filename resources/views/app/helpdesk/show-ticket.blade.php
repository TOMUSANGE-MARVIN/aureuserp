@extends('app.layouts.app')
@section('title', 'Ticket #' . $ticket->id)
@section('breadcrumb', 'Helpdesk / Ticket #' . $ticket->id)

@section('content')
@if(session('success'))
<div class="bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-xl p-4 text-sm text-green-800 dark:text-green-300">{{ session('success') }}</div>
@endif
@if(session('error'))
<div class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-xl p-4 text-sm text-red-800 dark:text-red-300">{{ session('error') }}</div>
@endif

@php
    $statusColors = ['open'=>'bg-yellow-100 dark:bg-yellow-900/30 text-yellow-700 dark:text-yellow-400','in_progress'=>'bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-400','resolved'=>'bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400','closed'=>'bg-gray-100 dark:bg-white/5 text-gray-500 dark:text-gray-400'];
    $priorityColors = ['urgent'=>'bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-400','high'=>'bg-orange-100 dark:bg-orange-900/30 text-orange-700 dark:text-orange-400','medium'=>'bg-yellow-100 dark:bg-yellow-900/30 text-yellow-700 dark:text-yellow-400','low'=>'bg-gray-100 dark:bg-white/5 text-gray-600 dark:text-gray-400'];
@endphp

<div class="flex items-start justify-between gap-4">
    <div class="flex items-center gap-3 flex-wrap">
        <a href="{{ route('helpdesk.index') }}" class="text-sm text-gray-500 dark:text-gray-400 hover:underline">← Helpdesk</a>
        <span class="text-gray-300 dark:text-white/20">/</span>
        <h1 class="text-xl font-bold text-gray-900 dark:text-white">{{ $ticket->title }}</h1>
        <span class="px-2.5 py-0.5 text-xs rounded-full {{ $statusColors[$ticket->status]??'' }} capitalize font-medium">{{ str_replace('_',' ',$ticket->status) }}</span>
        <span class="px-2.5 py-0.5 text-xs rounded-full {{ $priorityColors[$ticket->priority]??'' }} capitalize">{{ $ticket->priority }}</span>
    </div>
    <div class="flex items-center gap-2 flex-shrink-0">
        @if($ticket->status !== 'closed')
        <form method="POST" action="{{ route('helpdesk.update', $ticket->id) }}" class="inline">
            @csrf @method('PUT')
            @if($ticket->status === 'open')
                <input type="hidden" name="status" value="in_progress">
                <button type="submit" class="px-3 py-1.5 text-xs bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition">Start</button>
            @elseif($ticket->status === 'in_progress')
                <input type="hidden" name="status" value="resolved">
                <button type="submit" class="px-3 py-1.5 text-xs bg-green-600 hover:bg-green-700 text-white rounded-lg transition">Resolve</button>
            @elseif($ticket->status === 'resolved')
                <input type="hidden" name="status" value="closed">
                <button type="submit" class="px-3 py-1.5 text-xs bg-gray-600 hover:bg-gray-700 text-white rounded-lg transition">Close</button>
            @endif
        </form>
        @endif
        <form method="POST" action="{{ route('helpdesk.delete', $ticket->id) }}" class="inline" onsubmit="return confirm('Delete this ticket?')">
            @csrf @method('DELETE')
            <button type="submit" class="px-3 py-1.5 text-xs border border-red-200 dark:border-red-800/50 text-red-500 dark:text-red-400 rounded-lg hover:bg-red-50 dark:hover:bg-red-900/20 transition">Delete</button>
        </form>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    {{-- Main content --}}
    <div class="lg:col-span-2 space-y-4">
        {{-- Description --}}
        @if($ticket->description)
        <div class="bg-white dark:bg-[#111118] rounded-2xl border border-gray-100 dark:border-white/5 p-5">
            <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide mb-3">Description</p>
            <p class="text-sm text-gray-700 dark:text-gray-300 whitespace-pre-wrap">{{ $ticket->description }}</p>
        </div>
        @endif

        {{-- Messages Thread --}}
        <div class="space-y-3">
            @foreach($messages as $msg)
            <div class="bg-white dark:bg-[#111118] rounded-2xl border {{ $msg->is_internal ? 'border-yellow-200 dark:border-yellow-800/50' : 'border-gray-100 dark:border-white/5' }} p-5">
                <div class="flex items-center justify-between mb-2">
                    <div class="flex items-center gap-2">
                        <div class="w-7 h-7 rounded-full bg-brand-100 dark:bg-brand-900/30 flex items-center justify-center text-xs font-bold text-brand-700 dark:text-brand-400">
                            {{ strtoupper(substr($msg->author_name ?? '?', 0, 1)) }}
                        </div>
                        <span class="text-sm font-medium text-gray-900 dark:text-white">{{ $msg->author_name ?? 'System' }}</span>
                        @if($msg->is_internal)
                            <span class="px-1.5 py-0.5 text-xs rounded bg-yellow-100 dark:bg-yellow-900/30 text-yellow-700 dark:text-yellow-400">Internal Note</span>
                        @endif
                    </div>
                    <span class="text-xs text-gray-400 dark:text-gray-500">{{ \Carbon\Carbon::parse($msg->created_at)->diffForHumans() }}</span>
                </div>
                <p class="text-sm text-gray-700 dark:text-gray-300 whitespace-pre-wrap">{{ $msg->body }}</p>
            </div>
            @endforeach
        </div>

        {{-- Reply Form --}}
        @if($ticket->status !== 'closed')
        <div class="bg-white dark:bg-[#111118] rounded-2xl border border-gray-100 dark:border-white/5 p-5">
            <h3 class="text-sm font-semibold text-gray-900 dark:text-white mb-3">Add Reply</h3>
            <form method="POST" action="{{ route('helpdesk.messages.store', $ticket->id) }}" class="space-y-3">
                @csrf
                <textarea name="body" rows="4" required placeholder="Write your reply…"
                          class="w-full px-3 py-2 text-sm border border-gray-200 dark:border-white/10 rounded-xl bg-white dark:bg-[#111118] text-gray-900 dark:text-white resize-none"></textarea>
                <div class="flex items-center justify-between">
                    <label class="flex items-center gap-2 text-sm text-gray-600 dark:text-gray-400 cursor-pointer">
                        <input type="checkbox" name="is_internal" value="1" class="rounded border-gray-300 text-yellow-500">
                        Internal note (not visible to customer)
                    </label>
                    <button type="submit" class="px-4 py-2 text-sm bg-brand-600 hover:bg-brand-700 text-white rounded-xl transition font-medium">Send Reply</button>
                </div>
            </form>
        </div>
        @endif
    </div>

    {{-- Sidebar --}}
    <div class="space-y-4">
        {{-- Quick Update --}}
        <div class="bg-white dark:bg-[#111118] rounded-2xl border border-gray-100 dark:border-white/5 p-5">
            <h3 class="text-sm font-semibold text-gray-900 dark:text-white mb-4">Ticket Info</h3>
            <form method="POST" action="{{ route('helpdesk.update', $ticket->id) }}" class="space-y-3">
                @csrf @method('PUT')
                <div>
                    <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Status</label>
                    <select name="status" class="w-full px-3 py-2 text-sm border border-gray-200 dark:border-white/10 rounded-xl bg-white dark:bg-[#111118] text-gray-900 dark:text-white">
                        <option value="open" {{ $ticket->status==='open'?'selected':'' }}>Open</option>
                        <option value="in_progress" {{ $ticket->status==='in_progress'?'selected':'' }}>In Progress</option>
                        <option value="resolved" {{ $ticket->status==='resolved'?'selected':'' }}>Resolved</option>
                        <option value="closed" {{ $ticket->status==='closed'?'selected':'' }}>Closed</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Priority</label>
                    <select name="priority" class="w-full px-3 py-2 text-sm border border-gray-200 dark:border-white/10 rounded-xl bg-white dark:bg-[#111118] text-gray-900 dark:text-white">
                        <option value="low" {{ $ticket->priority==='low'?'selected':'' }}>Low</option>
                        <option value="medium" {{ $ticket->priority==='medium'?'selected':'' }}>Medium</option>
                        <option value="high" {{ $ticket->priority==='high'?'selected':'' }}>High</option>
                        <option value="urgent" {{ $ticket->priority==='urgent'?'selected':'' }}>Urgent</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Team</label>
                    <select name="team_id" class="w-full px-3 py-2 text-sm border border-gray-200 dark:border-white/10 rounded-xl bg-white dark:bg-[#111118] text-gray-900 dark:text-white">
                        <option value="">No team</option>
                        @foreach($teams as $team)<option value="{{ $team->id }}" {{ $ticket->team_id==$team->id?'selected':'' }}>{{ $team->name }}</option>@endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Assigned To</label>
                    <select name="assigned_to" class="w-full px-3 py-2 text-sm border border-gray-200 dark:border-white/10 rounded-xl bg-white dark:bg-[#111118] text-gray-900 dark:text-white">
                        <option value="">Unassigned</option>
                        @foreach($users as $u)<option value="{{ $u->id }}" {{ $ticket->assigned_to==$u->id?'selected':'' }}>{{ $u->name }}</option>@endforeach
                    </select>
                </div>
                <button type="submit" class="w-full px-4 py-2 text-sm bg-brand-600 hover:bg-brand-700 text-white rounded-xl transition font-medium">Save Changes</button>
            </form>
        </div>

        {{-- Customer Info --}}
        @if($ticket->customer_name || $ticket->customer_email)
        <div class="bg-white dark:bg-[#111118] rounded-2xl border border-gray-100 dark:border-white/5 p-5">
            <h3 class="text-sm font-semibold text-gray-900 dark:text-white mb-3">Customer</h3>
            @if($ticket->customer_name)<p class="text-sm font-medium text-gray-900 dark:text-white">{{ $ticket->customer_name }}</p>@endif
            @if($ticket->customer_email)<p class="text-sm text-brand-600 dark:text-brand-400">{{ $ticket->customer_email }}</p>@endif
        </div>
        @endif

        {{-- Meta --}}
        <div class="bg-white dark:bg-[#111118] rounded-2xl border border-gray-100 dark:border-white/5 p-5 space-y-2">
            <div class="flex justify-between text-sm">
                <span class="text-gray-500 dark:text-gray-400">Created</span>
                <span class="text-gray-700 dark:text-gray-300">{{ \Carbon\Carbon::parse($ticket->created_at)->format('M d, Y') }}</span>
            </div>
            <div class="flex justify-between text-sm">
                <span class="text-gray-500 dark:text-gray-400">By</span>
                <span class="text-gray-700 dark:text-gray-300">{{ $ticket->creator_name ?? '—' }}</span>
            </div>
            @if($ticket->resolved_at)
            <div class="flex justify-between text-sm">
                <span class="text-gray-500 dark:text-gray-400">Resolved</span>
                <span class="text-green-600 dark:text-green-400">{{ \Carbon\Carbon::parse($ticket->resolved_at)->format('M d, Y') }}</span>
            </div>
            @endif
            @if($ticket->type)
            <div class="flex justify-between text-sm">
                <span class="text-gray-500 dark:text-gray-400">Type</span>
                <span class="text-gray-700 dark:text-gray-300">{{ $ticket->type }}</span>
            </div>
            @endif
            <div class="flex justify-between text-sm">
                <span class="text-gray-500 dark:text-gray-400">Replies</span>
                <span class="text-gray-700 dark:text-gray-300">{{ $messages->count() }}</span>
            </div>
        </div>
    </div>
</div>
@endsection

@extends('app.layouts.app')
@section('title', 'Helpdesk Teams')
@section('breadcrumb', 'Helpdesk / Teams')

@section('content')
@if(session('success'))
<div class="bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-xl p-4 text-sm text-green-800 dark:text-green-300">{{ session('success') }}</div>
@endif
@if(session('error'))
<div class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-xl p-4 text-sm text-red-800 dark:text-red-300">{{ session('error') }}</div>
@endif

<div class="flex items-center justify-between">
    <div>
        <h1 class="text-xl font-bold text-gray-900 dark:text-white">Support Teams</h1>
        <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">Organise agents into teams and route tickets</p>
    </div>
    <a href="{{ route('helpdesk.index') }}" class="text-sm text-gray-500 dark:text-gray-400 hover:underline">← Helpdesk</a>
</div>

{{-- Create Team --}}
<div class="bg-white dark:bg-[#111118] rounded-2xl border border-gray-100 dark:border-white/5 p-5">
    <h2 class="text-sm font-semibold text-gray-900 dark:text-white mb-4">New Team</h2>
    <form method="POST" action="{{ route('helpdesk.teams.store') }}" class="flex flex-wrap gap-3 items-end">
        @csrf
        <div class="flex-1 min-w-48">
            <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Name *</label>
            <input type="text" name="name" required placeholder="e.g. Technical Support" value="{{ old('name') }}"
                   class="w-full px-3 py-2 text-sm border border-gray-200 dark:border-white/10 rounded-xl bg-white dark:bg-[#111118] text-gray-900 dark:text-white">
        </div>
        <div class="flex-1 min-w-48">
            <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Description</label>
            <input type="text" name="description" placeholder="Optional" value="{{ old('description') }}"
                   class="w-full px-3 py-2 text-sm border border-gray-200 dark:border-white/10 rounded-xl bg-white dark:bg-[#111118] text-gray-900 dark:text-white">
        </div>
        <button type="submit" class="px-4 py-2 text-sm bg-brand-600 hover:bg-brand-700 text-white rounded-xl transition font-medium">Create</button>
    </form>
</div>

{{-- Teams List --}}
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
    @forelse($teams as $team)
    <div class="bg-white dark:bg-[#111118] rounded-2xl border border-gray-100 dark:border-white/5 p-5">
        <div class="flex items-start justify-between mb-3">
            <div>
                <h3 class="font-semibold text-gray-900 dark:text-white">{{ $team->name }}</h3>
                @if($team->description)<p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">{{ $team->description }}</p>@endif
            </div>
            @if($team->is_active)
                <span class="px-2 py-0.5 text-xs rounded-full bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400">Active</span>
            @else
                <span class="px-2 py-0.5 text-xs rounded-full bg-gray-100 dark:bg-white/5 text-gray-500">Inactive</span>
            @endif
        </div>
        <div class="flex gap-4 text-xs text-gray-500 dark:text-gray-400 mb-4">
            <span>{{ $team->member_count }} member{{ $team->member_count!=1?'s':'' }}</span>
            <span>{{ $team->open_tickets }} open ticket{{ $team->open_tickets!=1?'s':'' }}</span>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('helpdesk.teams.edit', $team->id) }}"
               class="flex-1 text-center px-3 py-1.5 text-xs bg-brand-600 hover:bg-brand-700 text-white rounded-lg transition font-medium">Manage</a>
            <form method="POST" action="{{ route('helpdesk.teams.delete', $team->id) }}" class="inline" onsubmit="return confirm('Delete team?')">
                @csrf @method('DELETE')
                <button type="submit" class="px-3 py-1.5 text-xs border border-red-200 dark:border-red-800/50 text-red-500 dark:text-red-400 rounded-lg hover:bg-red-50 dark:hover:bg-red-900/20 transition">Delete</button>
            </form>
        </div>
    </div>
    @empty
    <div class="col-span-full bg-white dark:bg-[#111118] rounded-2xl border border-gray-100 dark:border-white/5 p-12 text-center text-gray-400 dark:text-gray-500">
        No teams yet. Create your first team above.
    </div>
    @endforelse
</div>
@endsection

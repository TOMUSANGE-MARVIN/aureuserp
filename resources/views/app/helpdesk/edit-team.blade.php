@extends('app.layouts.app')
@section('title', 'Edit Team')
@section('breadcrumb', 'Helpdesk / Teams / ' . $team->name)

@section('content')
@if(session('success'))
<div class="bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-xl p-4 text-sm text-green-800 dark:text-green-300">{{ session('success') }}</div>
@endif

<div class="flex items-center justify-between">
    <h1 class="text-xl font-bold text-gray-900 dark:text-white">{{ $team->name }}</h1>
    <a href="{{ route('helpdesk.teams') }}" class="text-sm text-gray-500 dark:text-gray-400 hover:underline">← Teams</a>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    {{-- Team Details --}}
    <div class="bg-white dark:bg-[#111118] rounded-2xl border border-gray-100 dark:border-white/5 p-5">
        <h2 class="text-sm font-semibold text-gray-900 dark:text-white mb-4">Team Details</h2>
        <form method="POST" action="{{ route('helpdesk.teams.update', $team->id) }}" class="space-y-4">
            @csrf @method('PUT')
            <div>
                <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Name *</label>
                <input type="text" name="name" value="{{ $team->name }}" required
                       class="w-full px-3 py-2 text-sm border border-gray-200 dark:border-white/10 rounded-xl bg-white dark:bg-[#111118] text-gray-900 dark:text-white">
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Description</label>
                <textarea name="description" rows="3"
                          class="w-full px-3 py-2 text-sm border border-gray-200 dark:border-white/10 rounded-xl bg-white dark:bg-[#111118] text-gray-900 dark:text-white resize-none">{{ $team->description }}</textarea>
            </div>
            <div class="flex items-center gap-2">
                <input type="checkbox" name="is_active" id="is_active" value="1" {{ $team->is_active ? 'checked' : '' }}
                       class="rounded border-gray-300 text-brand-600">
                <label for="is_active" class="text-sm text-gray-700 dark:text-gray-300">Active</label>
            </div>
            <button type="submit" class="w-full px-4 py-2 text-sm bg-brand-600 hover:bg-brand-700 text-white rounded-xl transition font-medium">Save</button>
        </form>
    </div>

    {{-- Members --}}
    <div class="space-y-4">
        {{-- Add Member --}}
        <div class="bg-white dark:bg-[#111118] rounded-2xl border border-gray-100 dark:border-white/5 p-5">
            <h2 class="text-sm font-semibold text-gray-900 dark:text-white mb-4">Add Member</h2>
            <form method="POST" action="{{ route('helpdesk.teams.members.add', $team->id) }}" class="flex gap-2">
                @csrf
                <select name="user_id" required class="flex-1 px-3 py-2 text-sm border border-gray-200 dark:border-white/10 rounded-xl bg-white dark:bg-[#111118] text-gray-900 dark:text-white">
                    <option value="">Select a user…</option>
                    @foreach($users as $u)
                        @if(!in_array($u->id, $memberIds))
                            <option value="{{ $u->id }}">{{ $u->name }}</option>
                        @endif
                    @endforeach
                </select>
                <button type="submit" class="px-4 py-2 text-sm bg-brand-600 hover:bg-brand-700 text-white rounded-xl transition font-medium">Add</button>
            </form>
        </div>

        {{-- Member List --}}
        <div class="bg-white dark:bg-[#111118] rounded-2xl border border-gray-100 dark:border-white/5 overflow-hidden">
            <div class="px-5 py-3 border-b border-gray-100 dark:border-white/5">
                <h2 class="text-sm font-semibold text-gray-900 dark:text-white">Members ({{ $members->count() }})</h2>
            </div>
            @if($members->isEmpty())
            <div class="px-5 py-8 text-center text-sm text-gray-400 dark:text-gray-500">No members yet.</div>
            @else
            <ul class="divide-y divide-gray-50 dark:divide-white/[0.03]">
                @foreach($members as $member)
                <li class="flex items-center justify-between px-5 py-3">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-full bg-brand-100 dark:bg-brand-900/30 flex items-center justify-center text-xs font-bold text-brand-700 dark:text-brand-400">
                            {{ strtoupper(substr($member->name, 0, 1)) }}
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $member->name }}</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">{{ $member->email }}</p>
                        </div>
                    </div>
                    <form method="POST" action="{{ route('helpdesk.teams.members.remove', [$team->id, $member->id]) }}" onsubmit="return confirm('Remove member?')">
                        @csrf @method('DELETE')
                        <button type="submit" class="text-xs text-red-500 dark:text-red-400 hover:underline">Remove</button>
                    </form>
                </li>
                @endforeach
            </ul>
            @endif
        </div>
    </div>
</div>
@endsection

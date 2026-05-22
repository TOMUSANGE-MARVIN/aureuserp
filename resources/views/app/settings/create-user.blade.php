@extends('app.layouts.app')
@section('title', 'Add User')

@section('content')
<div class="flex gap-8">
    <aside class="w-56 flex-shrink-0">
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm p-4">
            <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-3">Settings</p>
            @include('app.settings._sidebar')
        </div>
    </aside>
    <div class="flex-1 max-w-xl space-y-6">
        <div class="flex items-center gap-4">
            <a href="/app/settings/users" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            </a>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Add User</h1>
        </div>

        @if(session('success'))
        <div class="bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg p-4 text-sm text-green-700 dark:text-green-400">
            {{ session('success') }}
        </div>
        @endif

        @if(session('error'))
        <div class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg p-4 text-sm text-red-700 dark:text-red-400">
            {{ session('error') }}
        </div>
        @endif

        @if($errors->any())
        <div class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg p-4 text-sm text-red-700 dark:text-red-400">
            <ul class="list-disc list-inside space-y-1">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
        </div>
        @endif

        {{-- Tabs --}}
        @php $activeTab = request('tab', 'create'); @endphp
        <div class="flex border-b border-gray-200 dark:border-gray-700">
            <a href="?tab=create"
               class="px-5 py-2.5 text-sm font-medium border-b-2 transition-colors {{ $activeTab === 'create' ? 'border-brand-600 text-brand-600 dark:text-brand-400' : 'border-transparent text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200' }}">
                Create Manually
            </a>
            <a href="?tab=invite"
               class="px-5 py-2.5 text-sm font-medium border-b-2 transition-colors {{ $activeTab === 'invite' ? 'border-brand-600 text-brand-600 dark:text-brand-400' : 'border-transparent text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200' }}">
                Invite via Email
            </a>
        </div>

        {{-- Create Manually Tab --}}
        @if($activeTab === 'create')
        <form method="POST" action="/app/settings/users" class="space-y-6">
            @csrf
            <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm p-6 space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Name <span class="text-red-500">*</span></label>
                    <input type="text" name="name" value="{{ old('name') }}" required class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white px-3 py-2 text-sm focus:ring-2 focus:ring-brand-500 focus:border-transparent">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Email <span class="text-red-500">*</span></label>
                    <input type="email" name="email" value="{{ old('email') }}" required class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white px-3 py-2 text-sm focus:ring-2 focus:ring-brand-500 focus:border-transparent">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Password <span class="text-red-500">*</span></label>
                    <input type="password" name="password" required class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white px-3 py-2 text-sm focus:ring-2 focus:ring-brand-500 focus:border-transparent">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Confirm Password <span class="text-red-500">*</span></label>
                    <input type="password" name="password_confirmation" required class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white px-3 py-2 text-sm focus:ring-2 focus:ring-brand-500 focus:border-transparent">
                </div>
            </div>

            {{-- Module Access --}}
            <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm p-6">
                <div class="flex items-start justify-between mb-4">
                    <div>
                        <h2 class="text-sm font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">Module Access</h2>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Select which modules this user can see in the sidebar.</p>
                    </div>
                    <div class="flex gap-2">
                        <button type="button" onclick="document.querySelectorAll('[name=\'modules[]\']').forEach(c=>c.checked=true)"
                                class="text-xs text-brand-600 hover:text-brand-700 font-medium">Select all</button>
                        <span class="text-gray-300 dark:text-gray-600">|</span>
                        <button type="button" onclick="document.querySelectorAll('[name=\'modules[]\']').forEach(c=>c.checked=false)"
                                class="text-xs text-gray-500 hover:text-gray-700 dark:hover:text-gray-300 font-medium">Clear all</button>
                    </div>
                </div>
                @php $grouped = collect($modules)->groupBy(fn($m) => $m['group']); @endphp
                <div class="space-y-5">
                    @foreach($grouped as $group => $items)
                    <div>
                        <p class="text-xs font-semibold text-gray-400 dark:text-gray-500 uppercase tracking-widest mb-2">{{ $group }}</p>
                        <div class="grid grid-cols-2 sm:grid-cols-3 gap-2">
                            @foreach($items as $key => $item)
                            <label class="flex items-center gap-2.5 p-2.5 rounded-lg border border-gray-200 dark:border-gray-700 cursor-pointer hover:border-brand-200 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                                <input type="checkbox" name="modules[]" value="{{ $key }}"
                                       {{ in_array($key, (array) old('modules', [])) ? 'checked' : '' }}
                                       class="w-4 h-4 rounded text-brand-600 border-gray-300 dark:border-gray-600 focus:ring-brand-500">
                                <span class="text-sm text-gray-700 dark:text-gray-300 select-none">{{ $item['label'] }}</span>
                            </label>
                            @endforeach
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>

            <div class="flex justify-end gap-3">
                <a href="/app/settings/users" class="px-4 py-2 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-lg text-sm font-medium hover:bg-gray-50 dark:hover:bg-gray-700">Cancel</a>
                <button type="submit" class="px-6 py-2 bg-brand-600 hover:bg-brand-700 text-white rounded-lg text-sm font-medium shadow-sm">Create User</button>
            </div>
        </form>
        @endif

        {{-- Invite via Email Tab --}}
        @if($activeTab === 'invite')
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm p-6">
            <div class="mb-6">
                <h2 class="text-base font-semibold text-gray-900 dark:text-white mb-1">Invite a new user</h2>
                <p class="text-sm text-gray-500 dark:text-gray-400">An email will be sent with a secure link allowing the recipient to set up their own account.</p>
            </div>
            <form method="POST" action="/app/settings/users/invite" class="space-y-5">
                @csrf
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Email Address <span class="text-red-500">*</span></label>
                    <input type="email" name="email" value="{{ old('email') }}" required placeholder="colleague@example.com"
                           class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white px-3 py-2 text-sm focus:ring-2 focus:ring-brand-500 focus:border-transparent">
                </div>
                <div class="flex items-start gap-3 p-3 bg-blue-50 dark:bg-blue-900/20 rounded-lg border border-blue-200 dark:border-blue-800">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 mt-0.5 text-blue-500 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <p class="text-xs text-blue-700 dark:text-blue-300">The invited user will receive an email with a link to create their password and activate their account.</p>
                </div>
                <div class="flex justify-end gap-3">
                    <a href="/app/settings/users" class="px-4 py-2 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-lg text-sm font-medium hover:bg-gray-50 dark:hover:bg-gray-700">Cancel</a>
                    <button type="submit" class="px-6 py-2 bg-brand-600 hover:bg-brand-700 text-white rounded-lg text-sm font-medium shadow-sm flex items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                        Send Invitation
                    </button>
                </div>
            </form>
        </div>

        {{-- Pending invitations list --}}
        @php
            $pendingInvitations = \Illuminate\Support\Facades\DB::table('user_invitations')
                ->orderByDesc('created_at')
                ->limit(10)
                ->get();
        @endphp
        @if($pendingInvitations->count() > 0)
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm p-6">
            <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-3">Recent Invitations</h3>
            <ul class="divide-y divide-gray-100 dark:divide-gray-700">
                @foreach($pendingInvitations as $inv)
                <li class="py-2.5 flex items-center justify-between">
                    <span class="text-sm text-gray-800 dark:text-gray-200">{{ $inv->email }}</span>
                    <span class="text-xs text-gray-400 dark:text-gray-500">{{ \Carbon\Carbon::parse($inv->created_at)->diffForHumans() }}</span>
                </li>
                @endforeach
            </ul>
        </div>
        @endif
        @endif

    </div>
</div>
@endsection


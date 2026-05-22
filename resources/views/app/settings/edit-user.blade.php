@extends('app.layouts.app')
@section('title', 'Edit User')

@section('content')
<div class="flex gap-8">
    <aside class="w-56 flex-shrink-0">
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm p-4">
            <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-3">Settings</p>
            @include('app.settings._sidebar')
        </div>
    </aside>
    <div class="flex-1 max-w-2xl space-y-6">
        <div class="flex items-center gap-4">
            <a href="/app/settings/users" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            </a>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Edit User</h1>
        </div>

        @if(session('success'))
        <div class="bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg p-4 text-sm text-green-700 dark:text-green-400">{{ session('success') }}</div>
        @endif
        @if($errors->any())
        <div class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg p-4 text-sm text-red-700 dark:text-red-400">
            <ul class="list-disc list-inside space-y-1">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
        </div>
        @endif

        <form method="POST" action="/app/settings/users/{{ $user->id }}" class="space-y-6">
            @csrf
            @method('PUT')

            {{-- Basic Info --}}
            <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm p-6 space-y-4">
                <h2 class="text-sm font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">Account Details</h2>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Name <span class="text-red-500">*</span></label>
                        <input type="text" name="name" value="{{ old('name', $user->name) }}" required
                               class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white px-3 py-2 text-sm focus:ring-2 focus:ring-brand-500 focus:border-transparent">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Email <span class="text-red-500">*</span></label>
                        <input type="email" name="email" value="{{ old('email', $user->email) }}" required
                               class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white px-3 py-2 text-sm focus:ring-2 focus:ring-brand-500 focus:border-transparent">
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">New Password <span class="text-gray-400 font-normal">(leave blank to keep)</span></label>
                        <input type="password" name="password"
                               class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white px-3 py-2 text-sm focus:ring-2 focus:ring-brand-500 focus:border-transparent">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Confirm New Password</label>
                        <input type="password" name="password_confirmation"
                               class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white px-3 py-2 text-sm focus:ring-2 focus:ring-brand-500 focus:border-transparent">
                    </div>
                </div>
            </div>

            {{-- Module Access --}}
            <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm p-6">
                <div class="flex items-start justify-between mb-4">
                    <div>
                        <h2 class="text-sm font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">Module Access</h2>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Check the modules this user can access in the sidebar.</p>
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
                            <label class="flex items-center gap-2.5 p-2.5 rounded-lg border cursor-pointer transition-colors
                                {{ in_array($key, $userModules) ? 'border-brand-300 bg-brand-50 dark:border-brand-700 dark:bg-brand-900/20' : 'border-gray-200 dark:border-gray-700 hover:border-brand-200 dark:hover:border-brand-800 hover:bg-gray-50 dark:hover:bg-gray-700/50' }}"
                                 x-data
                                 @click="$el.classList.toggle('border-brand-300'); $el.classList.toggle('bg-brand-50'); $el.classList.toggle('dark:border-brand-700'); $el.classList.toggle('dark:bg-brand-900/20'); $el.classList.toggle('border-gray-200'); $el.classList.toggle('dark:border-gray-700')">
                                <input type="checkbox" name="modules[]" value="{{ $key }}"
                                       {{ in_array($key, $userModules) ? 'checked' : '' }}
                                       class="w-4 h-4 rounded text-brand-600 border-gray-300 dark:border-gray-600 focus:ring-brand-500">
                                <span class="text-sm text-gray-700 dark:text-gray-300 select-none">{{ $item['label'] }}</span>
                            </label>
                            @endforeach
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>

            <div class="flex items-center justify-between">
                <form method="POST" action="/app/settings/users/{{ $user->id }}" onsubmit="return confirm('Delete this user? This cannot be undone.')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="px-4 py-2 text-red-600 hover:text-red-700 dark:text-red-400 text-sm font-medium flex items-center gap-1.5">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                        Delete User
                    </button>
                </form>
                <div class="flex gap-3">
                    <a href="/app/settings/users" class="px-4 py-2 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-lg text-sm font-medium hover:bg-gray-50 dark:hover:bg-gray-700">Cancel</a>
                    <button type="submit" class="px-6 py-2 bg-brand-600 hover:bg-brand-700 text-white rounded-lg text-sm font-medium shadow-sm">Save Changes</button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

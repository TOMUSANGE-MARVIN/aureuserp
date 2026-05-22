@extends('app.layouts.app')
@section('title', 'Settings')

@section('content')
<div class="flex gap-8">
    <aside class="w-56 flex-shrink-0">
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm p-4">
            <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-3">Settings</p>
            @include('app.settings._sidebar')
        </div>
    </aside>

    <div class="flex-1 space-y-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">General Settings</h1>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Platform configuration and management</p>
        </div>

        @if(session('success'))
        <div class="bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg p-4 text-sm text-green-700 dark:text-green-400">
            {{ session('success') }}
        </div>
        @endif

        {{-- Default Currency --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm p-6">
            <h2 class="text-sm font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider mb-4">Company Defaults</h2>
            <form method="POST" action="/app/settings" class="space-y-4">
                @csrf
                <div class="max-w-sm">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Default Currency</label>
                    <select name="default_currency_id"
                            class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white px-3 py-2 text-sm focus:ring-2 focus:ring-brand-500 focus:border-transparent">
                        @foreach($currencies as $cur)
                        <option value="{{ $cur->id }}" {{ $company && $company->currency_id == $cur->id ? 'selected' : '' }}>
                            {{ $cur->name }} ({{ $cur->symbol }})
                        </option>
                        @endforeach
                    </select>
                    <p class="text-xs text-gray-400 mt-1">Used as the base currency across invoices, payroll, and reports.</p>
                </div>
                <div>
                    <button type="submit" class="px-5 py-2 bg-brand-600 hover:bg-brand-700 text-white rounded-lg text-sm font-medium shadow-sm">
                        Save Changes
                    </button>
                </div>
            </form>
        </div>

        {{-- Stats --}}
        <div class="grid grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach([
                ['label'=>'Users','value'=>$stats['users'],'href'=>'/app/settings/users','color'=>'brand',
                 'icon'=>'<svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>'],
                ['label'=>'Roles','value'=>$stats['roles'],'href'=>'/app/settings/roles','color'=>'purple',
                 'icon'=>'<svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>'],
                ['label'=>'Active Currencies','value'=>$stats['currencies'],'href'=>'/app/settings/currencies','color'=>'green',
                 'icon'=>'<svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>'],
            ] as $s)
            <a href="{{ $s['href'] }}" class="bg-white dark:bg-gray-800 rounded-xl p-5 border border-gray-200 dark:border-gray-700 shadow-sm hover:shadow-md hover:border-brand-300 dark:hover:border-brand-600 transition-all group">
                <div class="flex items-center justify-between mb-3">
                    <div class="text-{{ $s['color'] }}-600 dark:text-{{ $s['color'] }}-400 group-hover:scale-110 transition-transform">{!! $s['icon'] !!}</div>
                </div>
                <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $s['value'] }}</p>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">{{ $s['label'] }}</p>
            </a>
            @endforeach
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm p-6">
            <h2 class="text-base font-semibold text-gray-900 dark:text-white mb-4">Quick Links</h2>
            <div class="grid grid-cols-1 gap-3">
                <a href="/app/settings/users/create" class="flex items-center gap-3 p-3 rounded-lg border border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-brand-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/></svg>
                    <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Add User</span>
                </a>
                <a href="/app/settings/currencies" class="flex items-center gap-3 p-3 rounded-lg border border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Manage Currencies</span>
                </a>
            </div>
        </div>
    </div>
</div>
@endsection


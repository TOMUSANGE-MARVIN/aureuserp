@extends('app.layouts.app')
@section('title', 'Currencies')
@section('breadcrumb', 'Settings')

@section('content')
<div class="flex gap-8">
    <aside class="w-56 flex-shrink-0">
        <div class="bg-white dark:bg-[#111118] rounded-xl border border-gray-100 dark:border-white/5 shadow-sm p-4">
            <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-3">Settings</p>
            @include('app.settings._sidebar')
        </div>
    </aside>
    <div class="flex-1 space-y-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Currencies</h1>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Manage supported currencies for transactions</p>
        </div>

        @if(session('success'))
            <div class="px-4 py-3 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-xl text-sm text-green-700 dark:text-green-400">{{ session('success') }}</div>
        @endif

        <div class="bg-white dark:bg-[#111118] rounded-2xl border border-gray-100 dark:border-white/5 overflow-hidden">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-gray-100 dark:border-white/5">
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide">Name</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide">Symbol</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide">ISO</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide">Decimals</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide">Rounding</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide">Status</th>
                        <th class="px-4 py-3"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50 dark:divide-white/5">
                    @forelse($currencies as $currency)
                    <tr class="hover:bg-gray-50 dark:hover:bg-white/2 transition-colors">
                        <td class="px-4 py-3 font-medium text-gray-900 dark:text-white">{{ $currency->name }}</td>
                        <td class="px-4 py-3 text-gray-900 dark:text-white font-semibold">{{ $currency->symbol }}</td>
                        <td class="px-4 py-3 text-gray-500 dark:text-gray-400 font-mono text-xs">{{ $currency->iso_numeric ?? '—' }}</td>
                        <td class="px-4 py-3 text-gray-500 dark:text-gray-400">{{ $currency->decimal_places ?? 2 }}</td>
                        <td class="px-4 py-3 text-gray-500 dark:text-gray-400">{{ $currency->rounding ?? '0.01' }}</td>
                        <td class="px-4 py-3">
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium {{ $currency->active ? 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400' : 'bg-gray-100 text-gray-600 dark:bg-white/5 dark:text-gray-400' }}">
                                {{ $currency->active ? 'Active' : 'Inactive' }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-right">
                            <a href="{{ route('settings.currencies.edit', $currency->id) }}"
                               class="px-3 py-1 rounded-lg bg-brand-50 dark:bg-brand-900/20 hover:bg-brand-100 dark:hover:bg-brand-900/40 text-brand-600 dark:text-brand-400 text-xs font-medium transition-colors">
                                Edit
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="7" class="px-4 py-10 text-center text-gray-400 dark:text-gray-500">No currencies found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@extends('app.layouts.app')
@section('title', 'Companies')

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
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Companies</h1>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Tenant companies on the platform</p>
        </div>
        <form method="GET" action="/app/settings/companies" class="flex gap-3">
            <input type="text" name="search" value="{{ $search }}" placeholder="Search companies…"
                class="flex-1 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-white px-4 py-2 text-sm focus:ring-2 focus:ring-brand-500 focus:border-transparent">
            <button type="submit" class="px-4 py-2 bg-brand-600 hover:bg-brand-700 text-white rounded-lg text-sm font-medium">Search</button>
        </form>
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm overflow-hidden">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 dark:bg-gray-700/50">
                    <tr>
                        <th class="px-4 py-3 text-left font-semibold text-gray-600 dark:text-gray-300">Name</th>
                        <th class="px-4 py-3 text-left font-semibold text-gray-600 dark:text-gray-300">Subdomain</th>
                        <th class="px-4 py-3 text-left font-semibold text-gray-600 dark:text-gray-300">Status</th>
                        <th class="px-4 py-3 text-left font-semibold text-gray-600 dark:text-gray-300">Tenant</th>
                        <th class="px-4 py-3 text-left font-semibold text-gray-600 dark:text-gray-300">Trial Ends</th>
                        <th class="px-4 py-3 text-left font-semibold text-gray-600 dark:text-gray-300">Created</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                    @forelse($companies as $co)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/30 transition-colors">
                        <td class="px-4 py-3 font-medium text-gray-900 dark:text-white">{{ $co->name }}</td>
                        <td class="px-4 py-3 text-gray-600 dark:text-gray-300 font-mono text-xs">{{ $co->subdomain ?? '—' }}</td>
                        <td class="px-4 py-3">
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium {{ $co->is_active ? 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400' : 'bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-400' }}">
                                {{ $co->is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </td>
                        <td class="px-4 py-3">
                            @if($co->is_tenant)
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400">Tenant</span>
                            @else<span class="text-gray-400 text-xs">—</span>@endif
                        </td>
                        <td class="px-4 py-3 text-gray-500 dark:text-gray-400 text-xs">{{ $co->trial_ends_at ? date('d M Y', strtotime($co->trial_ends_at)) : '—' }}</td>
                        <td class="px-4 py-3 text-gray-500 dark:text-gray-400 text-xs">{{ $co->created_at ? date('d M Y', strtotime($co->created_at)) : '—' }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="6" class="px-4 py-10 text-center text-gray-500 dark:text-gray-400">No companies found.</td></tr>
                    @endforelse
                </tbody>
            </table>
            @if($companies->hasPages())
            <div class="px-4 py-3 border-t border-gray-100 dark:border-gray-700">{{ $companies->links() }}</div>
            @endif
        </div>
    </div>
</div>
@endsection

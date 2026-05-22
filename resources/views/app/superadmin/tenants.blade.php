@extends('app.layouts.app')
@section('title', 'Tenants')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Tenant Directory</h1>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">This section is scoped for super admin operations</p>
        </div>
    </div>

    <form method="GET" action="/superadmin/tenants" class="flex flex-col sm:flex-row gap-3">
        <input type="text" name="search" value="{{ $search }}" placeholder="Search by company name or subdomain"
               class="flex-1 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-white px-4 py-2 text-sm focus:ring-2 focus:ring-brand-500 focus:border-transparent">
        <select name="status" class="rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-white px-4 py-2 text-sm">
            <option value="all" {{ $status === 'all' ? 'selected' : '' }}>All</option>
            <option value="active" {{ $status === 'active' ? 'selected' : '' }}>Active</option>
            <option value="inactive" {{ $status === 'inactive' ? 'selected' : '' }}>Inactive</option>
            <option value="suspended" {{ $status === 'suspended' ? 'selected' : '' }}>Suspended</option>
        </select>
        <button type="submit" class="px-4 py-2 bg-brand-600 hover:bg-brand-700 text-white rounded-lg text-sm font-medium">Filter</button>
    </form>

    <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 dark:bg-gray-700/50">
                <tr>
                    <th class="px-4 py-3 text-left font-semibold text-gray-600 dark:text-gray-300">Company</th>
                    <th class="px-4 py-3 text-left font-semibold text-gray-600 dark:text-gray-300">Subdomain</th>
                    <th class="px-4 py-3 text-left font-semibold text-gray-600 dark:text-gray-300">Status</th>
                    <th class="px-4 py-3 text-left font-semibold text-gray-600 dark:text-gray-300">Trial Ends</th>
                    <th class="px-4 py-3 text-left font-semibold text-gray-600 dark:text-gray-300">Created</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                @forelse($tenants as $tenant)
                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/30 transition-colors">
                    <td class="px-4 py-3 font-medium text-gray-900 dark:text-white">{{ $tenant->name }}</td>
                    <td class="px-4 py-3 text-gray-600 dark:text-gray-300">{{ $tenant->subdomain ?? '—' }}</td>
                    <td class="px-4 py-3">
                        @if($tenant->suspended_at)
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400">Suspended</span>
                        @elseif($tenant->is_active)
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400">Active</span>
                        @else
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300">Inactive</span>
                        @endif
                    </td>
                    <td class="px-4 py-3 text-gray-500 dark:text-gray-400 text-xs">{{ $tenant->trial_ends_at ? date('d M Y', strtotime($tenant->trial_ends_at)) : '—' }}</td>
                    <td class="px-4 py-3 text-gray-500 dark:text-gray-400 text-xs">{{ $tenant->created_at ? date('d M Y', strtotime($tenant->created_at)) : '—' }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-4 py-10 text-center text-gray-500 dark:text-gray-400">No tenants found.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
        @if($tenants->hasPages())
        <div class="px-4 py-3 border-t border-gray-100 dark:border-gray-700">{{ $tenants->links() }}</div>
        @endif
    </div>
</div>
@endsection

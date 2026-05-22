@extends('app.layouts.app')
@section('title', 'Roles')

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
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Roles</h1>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">User permission roles</p>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm overflow-hidden">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 dark:bg-gray-700/50">
                    <tr>
                        <th class="px-4 py-3 text-left font-semibold text-gray-600 dark:text-gray-300">Name</th>
                        <th class="px-4 py-3 text-left font-semibold text-gray-600 dark:text-gray-300">Guard</th>
                        <th class="px-4 py-3 text-left font-semibold text-gray-600 dark:text-gray-300">Created</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                    @forelse($roles as $role)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/30 transition-colors">
                        <td class="px-4 py-3 font-medium text-gray-900 dark:text-white capitalize">{{ $role->name }}</td>
                        <td class="px-4 py-3 text-gray-600 dark:text-gray-300 font-mono text-xs">{{ $role->guard_name ?? 'web' }}</td>
                        <td class="px-4 py-3 text-gray-500 dark:text-gray-400 text-xs">{{ $role->created_at ? date('d M Y', strtotime($role->created_at)) : '—' }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="3" class="px-4 py-10 text-center text-gray-500 dark:text-gray-400">No roles found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

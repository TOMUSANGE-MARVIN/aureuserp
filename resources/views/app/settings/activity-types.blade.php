@extends('app.layouts.app')
@section('title', 'Activity Types')

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
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Activity Types</h1>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Available activity and task types</p>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm overflow-hidden">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 dark:bg-gray-700/50">
                    <tr>
                        <th class="px-4 py-3 text-left font-semibold text-gray-600 dark:text-gray-300">Name</th>
                        <th class="px-4 py-3 text-left font-semibold text-gray-600 dark:text-gray-300">Category</th>
                        <th class="px-4 py-3 text-left font-semibold text-gray-600 dark:text-gray-300">Delay</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                    @forelse($types as $type)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/30 transition-colors">
                        <td class="px-4 py-3 font-medium text-gray-900 dark:text-white">{{ $type->name }}</td>
                        <td class="px-4 py-3 text-gray-600 dark:text-gray-300 capitalize">{{ $type->category ?? '—' }}</td>
                        <td class="px-4 py-3 text-gray-600 dark:text-gray-300">{{ isset($type->delay_count) ? $type->delay_count.' '.($type->delay_unit??'days') : '—' }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="3" class="px-4 py-10 text-center text-gray-500 dark:text-gray-400">No activity types found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

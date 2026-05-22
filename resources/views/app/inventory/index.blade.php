@extends('app.layouts.app')
@section('title', 'Inventory Operations')
@section('breadcrumb')
<nav class="text-sm text-gray-500 dark:text-gray-400">
    <a href="/app/dashboard" class="hover:text-brand-600">Home</a>
    <span class="mx-2">/</span>
    <span class="text-gray-900 dark:text-white">Inventory</span>
</nav>
@endsection
@section('content')

@if(session('success'))
<div class="mb-4 px-4 py-3 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-xl text-green-700 dark:text-green-400 text-sm">{{ session('success') }}</div>
@endif
@if(session('error'))
<div class="mb-4 px-4 py-3 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-xl text-red-700 dark:text-red-400 text-sm">{{ session('error') }}</div>
@endif

<!-- Page Header -->
<div class="flex items-center justify-between mb-6">
    <div>
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Inventory Operations</h1>
        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Track receipts, deliveries, and internal transfers</p>
    </div>
</div>

<!-- Stat Cards -->
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
    <div class="bg-white dark:bg-[#111118] rounded-2xl shadow-sm border border-gray-100 dark:border-white/5 p-6">
        <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Total Operations</p>
        <p class="text-3xl font-bold text-gray-900 dark:text-white mt-2">{{ number_format($total) }}</p>
    </div>
    <div class="bg-white dark:bg-[#111118] rounded-2xl shadow-sm border border-gray-100 dark:border-white/5 p-6">
        <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Receipts</p>
        <p class="text-3xl font-bold text-green-600 dark:text-green-400 mt-2">{{ number_format($receipts) }}</p>
    </div>
    <div class="bg-white dark:bg-[#111118] rounded-2xl shadow-sm border border-gray-100 dark:border-white/5 p-6">
        <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Deliveries</p>
        <p class="text-3xl font-bold text-blue-600 dark:text-blue-400 mt-2">{{ number_format($deliveries) }}</p>
    </div>
    <div class="bg-white dark:bg-[#111118] rounded-2xl shadow-sm border border-gray-100 dark:border-white/5 p-6">
        <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Done</p>
        <p class="text-3xl font-bold text-purple-600 dark:text-purple-400 mt-2">{{ number_format($done) }}</p>
    </div>
</div>

<!-- Filter Tabs -->
<div class="bg-white dark:bg-[#111118] rounded-2xl shadow-sm border border-gray-100 dark:border-white/5 p-4 mb-6">
    <div class="flex gap-1 flex-wrap">
        @foreach(['all' => 'All', 'receipts' => 'Receipts', 'deliveries' => 'Deliveries', 'internal' => 'Internal', 'done' => 'Done'] as $key => $label)
        <a href="{{ request()->fullUrlWithQuery(['filter' => $key, 'page' => 1]) }}"
           class="px-3 py-1.5 rounded-lg text-sm font-medium transition-colors {{ $filter === $key ? 'bg-brand-600 text-white' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-white/5' }}">
            {{ $label }}
        </a>
        @endforeach
    </div>
</div>

<!-- Table -->
<div class="bg-white dark:bg-[#111118] rounded-2xl shadow-sm border border-gray-100 dark:border-white/5 overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-gray-50 dark:bg-white/3 border-b border-gray-100 dark:border-white/5">
            <tr>
                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Reference</th>
                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Type</th>
                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Origin</th>
                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Partner</th>
                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Scheduled</th>
                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">State</th>
                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-50 dark:divide-white/3">
            @forelse($operations as $op)
            <tr class="hover:bg-gray-50/50 dark:hover:bg-white/2 transition-colors">
                <td class="px-4 py-3 text-gray-700 dark:text-gray-300">
                    <a href="{{ route('inventory.show', $op->id) }}" class="font-medium text-brand-600 hover:text-brand-700 dark:text-brand-400">
                        {{ $op->name }}
                    </a>
                </td>
                <td class="px-4 py-3 text-gray-700 dark:text-gray-300">
                    <div>{{ $op->operation_type_name ?? '—' }}</div>
                    <div class="text-xs text-gray-400 mt-0.5">
                        @if($op->move_type === 'incoming') Receipt
                        @elseif($op->move_type === 'outgoing') Delivery
                        @elseif($op->move_type === 'internal') Internal
                        @else {{ $op->move_type }}
                        @endif
                    </div>
                </td>
                <td class="px-4 py-3 text-gray-700 dark:text-gray-300">{{ $op->origin ?? '—' }}</td>
                <td class="px-4 py-3 text-gray-700 dark:text-gray-300">{{ $op->partner_name ?? '—' }}</td>
                <td class="px-4 py-3 text-gray-700 dark:text-gray-300">
                    {{ $op->scheduled_at ? \Carbon\Carbon::parse($op->scheduled_at)->format('M d, Y') : '—' }}
                </td>
                <td class="px-4 py-3">
                    @if($op->state === 'done')
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400">Done</span>
                    @elseif($op->state === 'assigned')
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 dark:bg-purple-900/30 text-purple-700 dark:text-purple-400">Ready</span>
                    @elseif($op->state === 'confirmed')
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-400">Confirmed</span>
                    @elseif($op->state === 'waiting')
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 dark:bg-yellow-900/30 text-yellow-700 dark:text-yellow-400">Waiting</span>
                    @elseif($op->state === 'cancel')
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-400">Cancelled</span>
                    @else
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 dark:bg-white/10 text-gray-600 dark:text-gray-400">Draft</span>
                    @endif
                </td>
                <td class="px-4 py-3">
                    <a href="{{ route('inventory.show', $op->id) }}" class="px-3 py-1 bg-gray-100 dark:bg-white/5 hover:bg-gray-200 dark:hover:bg-white/10 text-gray-700 dark:text-gray-300 rounded-lg text-xs font-medium transition-colors">View</a>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="7" class="px-4 py-12 text-center text-gray-400 dark:text-gray-500">
                    <div class="text-4xl mb-3">📦</div>
                    <p class="font-medium">No inventory operations found</p>
                    <p class="text-sm mt-1">No operations match the current filter.</p>
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
    @if($operations->hasPages())
    <div class="px-4 py-3 border-t border-gray-100 dark:border-white/5">
        {{ $operations->links() }}
    </div>
    @endif
</div>
@endsection

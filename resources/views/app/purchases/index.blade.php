@extends('app.layouts.app')
@section('title', 'Purchase Orders')
@section('breadcrumb')
<nav class="text-sm text-gray-500 dark:text-gray-400">
    <a href="/app/dashboard" class="hover:text-brand-600">Home</a>
    <span class="mx-2">/</span>
    <span class="text-gray-900 dark:text-white">Purchase Orders</span>
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
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Purchase Orders</h1>
        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Manage vendor purchase orders and procurement</p>
    </div>
    <a href="{{ route('purchases.create') }}" class="px-4 py-2 bg-brand-600 hover:bg-brand-700 text-white rounded-xl text-sm font-medium transition-colors">
        + New Purchase Order
    </a>
</div>

<!-- Stat Cards -->
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
    <div class="bg-white dark:bg-[#111118] rounded-2xl shadow-sm border border-gray-100 dark:border-white/5 p-6">
        <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Total Orders</p>
        <p class="text-3xl font-bold text-gray-900 dark:text-white mt-2">{{ number_format($total) }}</p>
    </div>
    <div class="bg-white dark:bg-[#111118] rounded-2xl shadow-sm border border-gray-100 dark:border-white/5 p-6">
        <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Draft</p>
        <p class="text-3xl font-bold text-gray-600 dark:text-gray-300 mt-2">{{ number_format($draft) }}</p>
    </div>
    <div class="bg-white dark:bg-[#111118] rounded-2xl shadow-sm border border-gray-100 dark:border-white/5 p-6">
        <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Confirmed</p>
        <p class="text-3xl font-bold text-green-600 dark:text-green-400 mt-2">{{ number_format($confirmed) }}</p>
    </div>
    <div class="bg-white dark:bg-[#111118] rounded-2xl shadow-sm border border-gray-100 dark:border-white/5 p-6">
        <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Total Spend</p>
        <p class="text-3xl font-bold text-brand-600 dark:text-brand-400 mt-2">${{ number_format($spend, 2) }}</p>
    </div>
</div>

<!-- Filters & Search -->
<div class="bg-white dark:bg-[#111118] rounded-2xl shadow-sm border border-gray-100 dark:border-white/5 p-4 mb-6">
    <div class="flex flex-col sm:flex-row gap-4 items-start sm:items-center justify-between">
        <div class="flex gap-1 flex-wrap">
            @foreach(['all' => 'All', 'draft' => 'Draft', 'purchase' => 'Confirmed', 'done' => 'Done', 'cancel' => 'Cancelled'] as $key => $label)
            <a href="{{ request()->fullUrlWithQuery(['filter' => $key, 'page' => 1]) }}"
               class="px-3 py-1.5 rounded-lg text-sm font-medium transition-colors {{ $filter === $key ? 'bg-brand-600 text-white' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-white/5' }}">
                {{ $label }}
            </a>
            @endforeach
        </div>
        <form method="GET" action="{{ route('purchases.index') }}" class="flex gap-2">
            <input type="hidden" name="filter" value="{{ $filter }}">
            <input type="text" name="search" value="{{ $search }}" placeholder="Search orders…"
                   class="w-full px-3 py-2 rounded-xl border border-gray-200 dark:border-white/10 bg-white dark:bg-[#1a1a27] text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-brand-500 text-sm">
            <button type="submit" class="px-4 py-2 bg-brand-600 hover:bg-brand-700 text-white rounded-xl text-sm font-medium transition-colors">Search</button>
            @if($search)
            <a href="{{ route('purchases.index', ['filter' => $filter]) }}" class="px-4 py-2 bg-gray-100 dark:bg-white/5 hover:bg-gray-200 dark:hover:bg-white/10 text-gray-700 dark:text-gray-300 rounded-xl text-sm font-medium transition-colors">Clear</a>
            @endif
        </form>
    </div>
</div>

<!-- Table -->
<div class="bg-white dark:bg-[#111118] rounded-2xl shadow-sm border border-gray-100 dark:border-white/5 overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-gray-50 dark:bg-white/3 border-b border-gray-100 dark:border-white/5">
            <tr>
                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">PO #</th>
                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Vendor</th>
                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Date</th>
                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Total</th>
                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Status</th>
                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Priority</th>
                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Invoice</th>
                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-50 dark:divide-white/3">
            @forelse($orders as $order)
            <tr class="hover:bg-gray-50/50 dark:hover:bg-white/2 transition-colors">
                <td class="px-4 py-3 text-gray-700 dark:text-gray-300">
                    <a href="{{ route('purchases.show', $order->id) }}" class="font-medium text-brand-600 hover:text-brand-700 dark:text-brand-400">
                        {{ $order->name }}
                    </a>
                </td>
                <td class="px-4 py-3 text-gray-700 dark:text-gray-300">{{ $order->partner_name ?? '—' }}</td>
                <td class="px-4 py-3 text-gray-700 dark:text-gray-300">
                    {{ $order->ordered_at ? \Carbon\Carbon::parse($order->ordered_at)->format('M d, Y') : '—' }}
                </td>
                <td class="px-4 py-3 text-gray-700 dark:text-gray-300 font-medium">${{ number_format($order->total_amount ?? 0, 2) }}</td>
                <td class="px-4 py-3">
                    @if($order->state === 'purchase')
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400">Confirmed</span>
                    @elseif($order->state === 'done')
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-400">Done</span>
                    @elseif($order->state === 'cancel')
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-400">Cancelled</span>
                    @else
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 dark:bg-white/10 text-gray-600 dark:text-gray-400">Draft</span>
                    @endif
                </td>
                <td class="px-4 py-3">
                    @if(($order->priority ?? 'normal') === 'urgent')
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 dark:bg-yellow-900/30 text-yellow-700 dark:text-yellow-400">Urgent</span>
                    @else
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 dark:bg-white/10 text-gray-600 dark:text-gray-400">Normal</span>
                    @endif
                </td>
                <td class="px-4 py-3 text-gray-700 dark:text-gray-300 capitalize">{{ $order->invoice_status ?? '—' }}</td>
                <td class="px-4 py-3">
                    <div class="flex items-center gap-2">
                        <a href="{{ route('purchases.show', $order->id) }}" class="px-3 py-1 bg-gray-100 dark:bg-white/5 hover:bg-gray-200 dark:hover:bg-white/10 text-gray-700 dark:text-gray-300 rounded-lg text-xs font-medium transition-colors">View</a>
                        <a href="{{ route('purchases.edit', $order->id) }}" class="px-3 py-1 bg-brand-50 dark:bg-brand-900/20 hover:bg-brand-100 text-brand-700 dark:text-brand-400 rounded-lg text-xs font-medium transition-colors">Edit</a>
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="8" class="px-4 py-12 text-center text-gray-400 dark:text-gray-500">
                    <div class="text-4xl mb-3">🛒</div>
                    <p class="font-medium">No purchase orders found</p>
                    <p class="text-sm mt-1">Create your first purchase order to get started.</p>
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
    @if($orders->hasPages())
    <div class="px-4 py-3 border-t border-gray-100 dark:border-white/5">
        {{ $orders->links() }}
    </div>
    @endif
</div>
@endsection

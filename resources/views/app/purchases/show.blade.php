@extends('app.layouts.app')
@section('title', $order->name)
@section('breadcrumb')
<nav class="text-sm text-gray-500 dark:text-gray-400">
    <a href="/app/dashboard" class="hover:text-brand-600">Home</a>
    <span class="mx-2">/</span>
    <a href="{{ route('purchases.index') }}" class="hover:text-brand-600">Purchase Orders</a>
    <span class="mx-2">/</span>
    <span class="text-gray-900 dark:text-white">{{ $order->name }}</span>
</nav>
@endsection
@section('content')

@if(session('success'))
<div class="mb-4 px-4 py-3 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-xl text-green-700 dark:text-green-400 text-sm">{{ session('success') }}</div>
@endif

<!-- Header -->
<div class="flex items-start justify-between mb-6">
    <div>
        <div class="flex items-center gap-3">
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">{{ $order->name }}</h1>
            @if($order->state === 'purchase')
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400">Confirmed</span>
            @elseif($order->state === 'done')
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-400">Done</span>
            @elseif($order->state === 'cancel')
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-400">Cancelled</span>
            @else
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 dark:bg-white/10 text-gray-600 dark:text-gray-400">Draft</span>
            @endif
            @if(($order->priority ?? 'normal') === 'urgent')
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 dark:bg-yellow-900/30 text-yellow-700 dark:text-yellow-400">Urgent</span>
            @endif
        </div>
        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
            {{ $order->partner_name ?? 'No vendor' }} &mdash;
            {{ $order->ordered_at ? \Carbon\Carbon::parse($order->ordered_at)->format('M d, Y') : '—' }}
        </p>
    </div>
    <div class="flex items-center gap-2">
        <a href="{{ route('purchases.edit', $order->id) }}" class="px-4 py-2 bg-brand-600 hover:bg-brand-700 text-white rounded-xl text-sm font-medium transition-colors">Edit</a>
        <a href="{{ route('purchases.index') }}" class="px-4 py-2 bg-gray-100 dark:bg-white/5 hover:bg-gray-200 dark:hover:bg-white/10 text-gray-700 dark:text-gray-300 rounded-xl text-sm font-medium transition-colors">← Back</a>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
    <!-- Details -->
    <div class="bg-white dark:bg-[#111118] rounded-2xl shadow-sm border border-gray-100 dark:border-white/5 p-6">
        <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider mb-4">Order Details</h3>
        <dl class="space-y-3">
            <div>
                <dt class="text-xs text-gray-400 dark:text-gray-500">Vendor</dt>
                <dd class="text-sm font-medium text-gray-900 dark:text-white mt-0.5">{{ $order->partner_name ?? '—' }}</dd>
            </div>
            <div>
                <dt class="text-xs text-gray-400 dark:text-gray-500">Order Date</dt>
                <dd class="text-sm text-gray-700 dark:text-gray-300 mt-0.5">{{ $order->ordered_at ? \Carbon\Carbon::parse($order->ordered_at)->format('M d, Y H:i') : '—' }}</dd>
            </div>
            <div>
                <dt class="text-xs text-gray-400 dark:text-gray-500">Approved At</dt>
                <dd class="text-sm text-gray-700 dark:text-gray-300 mt-0.5">{{ $order->approved_at ? \Carbon\Carbon::parse($order->approved_at)->format('M d, Y H:i') : '—' }}</dd>
            </div>
            <div>
                <dt class="text-xs text-gray-400 dark:text-gray-500">Priority</dt>
                <dd class="mt-0.5">
                    @if(($order->priority ?? 'normal') === 'urgent')
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 dark:bg-yellow-900/30 text-yellow-700 dark:text-yellow-400">Urgent</span>
                    @else
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 dark:bg-white/10 text-gray-600 dark:text-gray-400">Normal</span>
                    @endif
                </dd>
            </div>
            <div>
                <dt class="text-xs text-gray-400 dark:text-gray-500">Invoice Status</dt>
                <dd class="text-sm text-gray-700 dark:text-gray-300 mt-0.5 capitalize">{{ $order->invoice_status ?? '—' }}</dd>
            </div>
            <div>
                <dt class="text-xs text-gray-400 dark:text-gray-500">Receipt Status</dt>
                <dd class="text-sm text-gray-700 dark:text-gray-300 mt-0.5 capitalize">{{ $order->receipt_status ?? '—' }}</dd>
            </div>
        </dl>
    </div>

    <!-- Amounts -->
    <div class="bg-white dark:bg-[#111118] rounded-2xl shadow-sm border border-gray-100 dark:border-white/5 p-6">
        <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider mb-4">Amounts</h3>
        <dl class="space-y-3">
            <div class="flex justify-between items-center">
                <dt class="text-sm text-gray-500 dark:text-gray-400">Untaxed Amount</dt>
                <dd class="text-sm font-medium text-gray-900 dark:text-white">${{ number_format($order->untaxed_amount ?? 0, 2) }}</dd>
            </div>
            <div class="flex justify-between items-center">
                <dt class="text-sm text-gray-500 dark:text-gray-400">Tax</dt>
                <dd class="text-sm font-medium text-gray-900 dark:text-white">${{ number_format($order->tax_amount ?? 0, 2) }}</dd>
            </div>
            <div class="flex justify-between items-center pt-3 border-t border-gray-100 dark:border-white/5">
                <dt class="text-sm font-semibold text-gray-700 dark:text-gray-300">Total</dt>
                <dd class="text-lg font-bold text-brand-600 dark:text-brand-400">${{ number_format($order->total_amount ?? 0, 2) }}</dd>
            </div>
        </dl>
    </div>
</div>

<!-- Order Lines -->
@if(count($lines) > 0)
<div class="bg-white dark:bg-[#111118] rounded-2xl shadow-sm border border-gray-100 dark:border-white/5 overflow-hidden">
    <div class="px-6 py-4 border-b border-gray-100 dark:border-white/5">
        <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">Order Lines</h3>
    </div>
    <table class="w-full text-sm">
        <thead class="bg-gray-50 dark:bg-white/3 border-b border-gray-100 dark:border-white/5">
            <tr>
                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Product / Description</th>
                <th class="px-4 py-3 text-right text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Quantity</th>
                <th class="px-4 py-3 text-right text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Unit Price</th>
                <th class="px-4 py-3 text-right text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Subtotal</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-50 dark:divide-white/3">
            @foreach($lines as $line)
            <tr>
                <td class="px-4 py-3 text-gray-700 dark:text-gray-300">{{ $line->name ?? '—' }}</td>
                <td class="px-4 py-3 text-gray-700 dark:text-gray-300 text-right">{{ number_format($line->product_qty ?? 0, 2) }}</td>
                <td class="px-4 py-3 text-gray-700 dark:text-gray-300 text-right">${{ number_format($line->price_unit ?? 0, 2) }}</td>
                <td class="px-4 py-3 text-gray-900 dark:text-white font-medium text-right">${{ number_format($line->price_subtotal ?? 0, 2) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endif
@endsection

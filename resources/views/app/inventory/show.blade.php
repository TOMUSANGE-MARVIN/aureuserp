@extends('app.layouts.app')
@section('title', $operation->name)
@section('breadcrumb')
<nav class="text-sm text-gray-500 dark:text-gray-400">
    <a href="/app/dashboard" class="hover:text-brand-600">Home</a>
    <span class="mx-2">/</span>
    <a href="{{ route('inventory.index') }}" class="hover:text-brand-600">Inventory</a>
    <span class="mx-2">/</span>
    <span class="text-gray-900 dark:text-white">{{ $operation->name }}</span>
</nav>
@endsection
@section('content')

@php
    $moveTypeLabel = match($operation->move_type ?? '') {
        'incoming' => 'Receipt',
        'outgoing' => 'Delivery',
        'internal' => 'Internal Transfer',
        default    => ucfirst($operation->move_type ?? '—'),
    };
@endphp

<!-- Header -->
<div class="flex items-start justify-between mb-6">
    <div>
        <div class="flex items-center gap-3">
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">{{ $operation->name }}</h1>
            @if($operation->state === 'done')
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400">Done</span>
            @elseif($operation->state === 'assigned')
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 dark:bg-purple-900/30 text-purple-700 dark:text-purple-400">Ready</span>
            @elseif($operation->state === 'confirmed')
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-400">Confirmed</span>
            @elseif($operation->state === 'waiting')
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 dark:bg-yellow-900/30 text-yellow-700 dark:text-yellow-400">Waiting</span>
            @elseif($operation->state === 'cancel')
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-400">Cancelled</span>
            @else
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 dark:bg-white/10 text-gray-600 dark:text-gray-400">Draft</span>
            @endif
            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-400">{{ $moveTypeLabel }}</span>
        </div>
        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
            {{ $operation->operation_type_name ?? '—' }}
            @if($operation->partner_name) &mdash; {{ $operation->partner_name }} @endif
        </p>
    </div>
    <a href="{{ route('inventory.index') }}" class="px-4 py-2 bg-gray-100 dark:bg-white/5 hover:bg-gray-200 dark:hover:bg-white/10 text-gray-700 dark:text-gray-300 rounded-xl text-sm font-medium transition-colors">← Back</a>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
    <!-- Operation Details -->
    <div class="bg-white dark:bg-[#111118] rounded-2xl shadow-sm border border-gray-100 dark:border-white/5 p-6">
        <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider mb-4">Operation Details</h3>
        <dl class="space-y-3">
            <div>
                <dt class="text-xs text-gray-400 dark:text-gray-500">Operation Type</dt>
                <dd class="text-sm font-medium text-gray-900 dark:text-white mt-0.5">{{ $operation->operation_type_name ?? '—' }}</dd>
            </div>
            <div>
                <dt class="text-xs text-gray-400 dark:text-gray-500">Move Type</dt>
                <dd class="text-sm text-gray-700 dark:text-gray-300 mt-0.5">{{ $moveTypeLabel }}</dd>
            </div>
            <div>
                <dt class="text-xs text-gray-400 dark:text-gray-500">Origin</dt>
                <dd class="text-sm text-gray-700 dark:text-gray-300 mt-0.5">{{ $operation->origin ?? '—' }}</dd>
            </div>
            <div>
                <dt class="text-xs text-gray-400 dark:text-gray-500">Partner</dt>
                <dd class="text-sm text-gray-700 dark:text-gray-300 mt-0.5">{{ $operation->partner_name ?? '—' }}</dd>
            </div>
            <div>
                <dt class="text-xs text-gray-400 dark:text-gray-500">Scheduled Date</dt>
                <dd class="text-sm text-gray-700 dark:text-gray-300 mt-0.5">{{ $operation->scheduled_at ? \Carbon\Carbon::parse($operation->scheduled_at)->format('M d, Y H:i') : '—' }}</dd>
            </div>
            <div>
                <dt class="text-xs text-gray-400 dark:text-gray-500">Closed Date</dt>
                <dd class="text-sm text-gray-700 dark:text-gray-300 mt-0.5">{{ $operation->closed_at ? \Carbon\Carbon::parse($operation->closed_at)->format('M d, Y H:i') : '—' }}</dd>
            </div>
        </dl>
    </div>

    <!-- Locations -->
    <div class="bg-white dark:bg-[#111118] rounded-2xl shadow-sm border border-gray-100 dark:border-white/5 p-6">
        <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider mb-4">Locations</h3>
        <dl class="space-y-4">
            <div>
                <dt class="text-xs text-gray-400 dark:text-gray-500 mb-1">Source Location</dt>
                <dd class="flex items-center gap-2">
                    <span class="text-lg">📤</span>
                    <span class="text-sm font-medium text-gray-900 dark:text-white">
                        {{ $sourceLocation->complete_name ?? ($sourceLocation->name ?? '—') }}
                    </span>
                </dd>
            </div>
            <div class="flex items-center text-gray-300 dark:text-gray-600">
                <div class="flex-1 h-px bg-gray-100 dark:bg-white/5"></div>
                <span class="mx-3 text-sm">→</span>
                <div class="flex-1 h-px bg-gray-100 dark:bg-white/5"></div>
            </div>
            <div>
                <dt class="text-xs text-gray-400 dark:text-gray-500 mb-1">Destination Location</dt>
                <dd class="flex items-center gap-2">
                    <span class="text-lg">📥</span>
                    <span class="text-sm font-medium text-gray-900 dark:text-white">
                        {{ $destLocation->complete_name ?? ($destLocation->name ?? '—') }}
                    </span>
                </dd>
            </div>
        </dl>
    </div>
</div>

<!-- Move Lines -->
@if(count($lines) > 0)
<div class="bg-white dark:bg-[#111118] rounded-2xl shadow-sm border border-gray-100 dark:border-white/5 overflow-hidden">
    <div class="px-6 py-4 border-b border-gray-100 dark:border-white/5">
        <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">Move Lines</h3>
    </div>
    <table class="w-full text-sm">
        <thead class="bg-gray-50 dark:bg-white/3 border-b border-gray-100 dark:border-white/5">
            <tr>
                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Product ID</th>
                <th class="px-4 py-3 text-right text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Quantity</th>
                <th class="px-4 py-3 text-right text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Done</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-50 dark:divide-white/3">
            @foreach($lines as $line)
            <tr>
                <td class="px-4 py-3 text-gray-700 dark:text-gray-300">{{ $line->product_id ?? '—' }}</td>
                <td class="px-4 py-3 text-gray-700 dark:text-gray-300 text-right">{{ number_format($line->quantity ?? 0, 2) }}</td>
                <td class="px-4 py-3 text-right">
                    @php $done = $line->qty_done ?? 0; $total = $line->quantity ?? 0; @endphp
                    <span class="{{ $done >= $total && $total > 0 ? 'text-green-600 dark:text-green-400' : 'text-gray-700 dark:text-gray-300' }} font-medium">
                        {{ number_format($done, 2) }}
                    </span>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endif
@endsection

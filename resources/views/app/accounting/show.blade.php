@extends('app.layouts.app')
@section('title', $move->name)
@section('breadcrumb')
<nav class="text-sm text-gray-500 dark:text-gray-400">
    <a href="/app/dashboard" class="hover:text-brand-600">Home</a>
    <span class="mx-2">/</span>
    <a href="{{ route('accounting.index') }}" class="hover:text-brand-600">Accounting</a>
    <span class="mx-2">/</span>
    <span class="text-gray-900 dark:text-white">{{ $move->name }}</span>
</nav>
@endsection
@section('content')

@php
$moveTypeLabel = match($move->move_type ?? '') {
    'out_invoice' => 'Customer Invoice',
    'in_invoice'  => 'Vendor Bill',
    'out_refund'  => 'Credit Note',
    'in_refund'   => 'Vendor Credit Note',
    'entry'       => 'Journal Entry',
    default       => ucfirst(str_replace('_', ' ', $move->move_type ?? '')),
};
@endphp

<!-- Header -->
<div class="flex items-start justify-between mb-6">
    <div>
        <div class="flex items-center gap-3 flex-wrap">
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">{{ $move->name }}</h1>
            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-400">{{ $moveTypeLabel }}</span>
            @if($move->state === 'posted')
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400">Posted</span>
            @elseif($move->state === 'cancel')
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-400">Cancelled</span>
            @else
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 dark:bg-white/10 text-gray-600 dark:text-gray-400">Draft</span>
            @endif
        </div>
        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
            {{ $move->partner_name ?? 'No partner' }}
            @if($move->invoice_date) &mdash; {{ \Carbon\Carbon::parse($move->invoice_date)->format('M d, Y') }} @endif
        </p>
    </div>
    <a href="{{ route('accounting.index') }}" class="px-4 py-2 bg-gray-100 dark:bg-white/5 hover:bg-gray-200 dark:hover:bg-white/10 text-gray-700 dark:text-gray-300 rounded-xl text-sm font-medium transition-colors">← Back</a>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
    <!-- Details -->
    <div class="bg-white dark:bg-[#111118] rounded-2xl shadow-sm border border-gray-100 dark:border-white/5 p-6">
        <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider mb-4">Details</h3>
        <dl class="space-y-3">
            <div>
                <dt class="text-xs text-gray-400 dark:text-gray-500">Partner</dt>
                <dd class="text-sm font-medium text-gray-900 dark:text-white mt-0.5">{{ $move->partner_name ?? '—' }}</dd>
            </div>
            <div>
                <dt class="text-xs text-gray-400 dark:text-gray-500">Document Type</dt>
                <dd class="text-sm text-gray-700 dark:text-gray-300 mt-0.5">{{ $moveTypeLabel }}</dd>
            </div>
            <div>
                <dt class="text-xs text-gray-400 dark:text-gray-500">Invoice Date</dt>
                <dd class="text-sm text-gray-700 dark:text-gray-300 mt-0.5">{{ $move->invoice_date ? \Carbon\Carbon::parse($move->invoice_date)->format('M d, Y') : '—' }}</dd>
            </div>
            <div>
                <dt class="text-xs text-gray-400 dark:text-gray-500">Due Date</dt>
                <dd class="text-sm text-gray-700 dark:text-gray-300 mt-0.5">{{ $move->invoice_date_due ? \Carbon\Carbon::parse($move->invoice_date_due)->format('M d, Y') : '—' }}</dd>
            </div>
            <div>
                <dt class="text-xs text-gray-400 dark:text-gray-500">Source / Origin</dt>
                <dd class="text-sm text-gray-700 dark:text-gray-300 mt-0.5">{{ $move->invoice_origin ?? '—' }}</dd>
            </div>
            <div>
                <dt class="text-xs text-gray-400 dark:text-gray-500">Payment Status</dt>
                <dd class="mt-0.5">
                    @php $ps = $move->payment_state ?? ''; @endphp
                    @if($ps === 'paid')
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400">Paid</span>
                    @elseif($ps === 'in_payment')
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 dark:bg-yellow-900/30 text-yellow-700 dark:text-yellow-400">In Payment</span>
                    @elseif($ps === 'not_paid')
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-400">Not Paid</span>
                    @elseif($ps === 'partial')
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-400">Partial</span>
                    @elseif($ps === 'reversed')
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 dark:bg-white/10 text-gray-600 dark:text-gray-400">Reversed</span>
                    @else
                        <span class="text-sm text-gray-500">{{ $ps ?: '—' }}</span>
                    @endif
                </dd>
            </div>
        </dl>
    </div>

    <!-- Amounts -->
    <div class="bg-white dark:bg-[#111118] rounded-2xl shadow-sm border border-gray-100 dark:border-white/5 p-6">
        <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider mb-4">Amounts</h3>
        <dl class="space-y-3">
            <div class="flex justify-between items-center">
                <dt class="text-sm text-gray-500 dark:text-gray-400">Total Amount</dt>
                <dd class="text-lg font-bold text-brand-600 dark:text-brand-400">${{ number_format($move->amount_total ?? 0, 2) }}</dd>
            </div>
            <div class="flex justify-between items-center pt-3 border-t border-gray-100 dark:border-white/5">
                <dt class="text-sm text-gray-500 dark:text-gray-400">Amount Due (Residual)</dt>
                <dd class="text-sm font-semibold {{ ($move->amount_residual ?? 0) > 0 ? 'text-red-600 dark:text-red-400' : 'text-green-600 dark:text-green-400' }}">
                    ${{ number_format($move->amount_residual ?? 0, 2) }}
                </dd>
            </div>
        </dl>
        @if($move->narration)
        <div class="mt-5 pt-5 border-t border-gray-100 dark:border-white/5">
            <dt class="text-xs text-gray-400 dark:text-gray-500 mb-1">Notes / Narration</dt>
            <dd class="text-sm text-gray-700 dark:text-gray-300 whitespace-pre-wrap">{{ $move->narration }}</dd>
        </div>
        @endif
    </div>
</div>

<!-- Journal Lines -->
@if(count($lines) > 0)
<div class="bg-white dark:bg-[#111118] rounded-2xl shadow-sm border border-gray-100 dark:border-white/5 overflow-hidden">
    <div class="px-6 py-4 border-b border-gray-100 dark:border-white/5">
        <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">Journal Lines</h3>
    </div>
    <table class="w-full text-sm">
        <thead class="bg-gray-50 dark:bg-white/3 border-b border-gray-100 dark:border-white/5">
            <tr>
                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Label</th>
                <th class="px-4 py-3 text-right text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Debit</th>
                <th class="px-4 py-3 text-right text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Credit</th>
                <th class="px-4 py-3 text-right text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Balance</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-50 dark:divide-white/3">
            @foreach($lines as $line)
            <tr>
                <td class="px-4 py-3 text-gray-700 dark:text-gray-300">{{ $line->name ?? '—' }}</td>
                <td class="px-4 py-3 text-gray-700 dark:text-gray-300 text-right">{{ number_format($line->debit ?? 0, 2) }}</td>
                <td class="px-4 py-3 text-gray-700 dark:text-gray-300 text-right">{{ number_format($line->credit ?? 0, 2) }}</td>
                <td class="px-4 py-3 font-medium text-right {{ ($line->balance ?? 0) >= 0 ? 'text-gray-900 dark:text-white' : 'text-red-600 dark:text-red-400' }}">
                    {{ number_format($line->balance ?? 0, 2) }}
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endif
@endsection

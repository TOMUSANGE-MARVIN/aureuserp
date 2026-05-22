@extends('superadmin.layouts.app')
@section('title', 'Subscriptions')
@section('breadcrumb', 'Subscriptions')

@section('content')
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-xl font-bold text-gray-900 dark:text-white">Subscriptions</h1>
            <p class="text-sm text-gray-500 mt-1">All tenant subscriptions — MRR: <span class="font-semibold text-emerald-600">${{ number_format($mrr, 2) }}</span></p>
        </div>
    </div>

    <div class="grid grid-cols-4 gap-4">
        @foreach([['label'=>'Total','value'=>$total,'color'=>'gray'],['label'=>'Active','value'=>$activeCount,'color'=>'green'],['label'=>'Trial','value'=>$trialCount,'color'=>'blue'],['label'=>'Cancelled','value'=>$cancelledCount,'color'=>'red']] as $s)
        <div class="bg-white dark:bg-[#0f0f1a] rounded-xl border border-gray-100 dark:border-white/5 px-4 py-3 flex items-center gap-3">
            <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $s['value'] }}</p>
            <p class="text-sm text-gray-500 dark:text-gray-400">{{ $s['label'] }}</p>
        </div>
        @endforeach
    </div>

    <form method="GET" action="/superadmin/subscriptions" class="flex gap-3">
        <select name="status" onchange="this.form.submit()"
                class="px-3 py-2 rounded-xl border border-gray-200 dark:border-white/10 bg-white dark:bg-[#0f0f1a] text-gray-900 dark:text-white text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
            <option value="">All Status</option>
            @foreach(['active','trial','cancelled','expired'] as $s)
                <option value="{{ $s }}" {{ $filter === $s ? 'selected' : '' }}>{{ ucfirst($s) }}</option>
            @endforeach
        </select>
    </form>

    <div class="bg-white dark:bg-[#0f0f1a] rounded-2xl border border-gray-100 dark:border-white/5 overflow-hidden">
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b border-gray-100 dark:border-white/5">
                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-400 uppercase tracking-wide">Organization</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-400 uppercase tracking-wide">Plan</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-400 uppercase tracking-wide">Amount</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-400 uppercase tracking-wide">Status</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-400 uppercase tracking-wide">Billing</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-400 uppercase tracking-wide">Trial Ends</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-400 uppercase tracking-wide">Started</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50 dark:divide-white/3">
                @forelse($subscriptions as $sub)
                    @php
                        $org  = collect($orgs)->firstWhere('id', $sub->company_id);
                        $plan = collect($plans)->firstWhere('id', $sub->plan_id);
                        $statusColor = match($sub->status) {
                            'active'    => 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400',
                            'trial'     => 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400',
                            'cancelled' => 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400',
                            default     => 'bg-gray-100 text-gray-600 dark:bg-white/5 dark:text-gray-400',
                        };
                    @endphp
                    <tr class="hover:bg-gray-50 dark:hover:bg-white/2 transition-colors">
                        <td class="px-5 py-3 font-medium text-gray-900 dark:text-white">{{ $org?->name ?? 'Unknown Org #'.$sub->company_id }}</td>
                        <td class="px-5 py-3 text-gray-500 dark:text-gray-400">{{ $plan?->name ?? 'Unknown Plan' }}</td>
                        <td class="px-5 py-3 font-semibold text-gray-900 dark:text-white">${{ $sub->amount }} <span class="text-xs font-normal text-gray-400">{{ $sub->currency }}</span></td>
                        <td class="px-5 py-3"><span class="inline-flex px-2 py-0.5 rounded-full text-xs font-medium capitalize {{ $statusColor }}">{{ $sub->status }}</span></td>
                        <td class="px-5 py-3 text-gray-500 dark:text-gray-400 capitalize text-xs">{{ $sub->billing_cycle }}</td>
                        <td class="px-5 py-3 text-gray-400 text-xs">{{ $sub->trial_ends_at ? \Carbon\Carbon::parse($sub->trial_ends_at)->format('M d, Y') : '—' }}</td>
                        <td class="px-5 py-3 text-gray-400 text-xs">{{ $sub->starts_at ? \Carbon\Carbon::parse($sub->starts_at)->format('M d, Y') : '—' }}</td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="px-5 py-8 text-center text-gray-400">No subscriptions found.</td></tr>
                @endforelse
            </tbody>
        </table>
        @if($subscriptions->hasPages())
            <div class="px-5 py-3 border-t border-gray-100 dark:border-white/5">{{ $subscriptions->links() }}</div>
        @endif
    </div>
@endsection

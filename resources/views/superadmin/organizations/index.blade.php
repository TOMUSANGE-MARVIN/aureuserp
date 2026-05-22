@extends('superadmin.layouts.app')
@section('title', 'Organizations')
@section('breadcrumb', 'Organizations')

@section('content')
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-xl font-bold text-gray-900 dark:text-white">Organizations</h1>
            <p class="text-sm text-gray-500 mt-1">{{ $total }} total tenant organizations</p>
        </div>
    </div>

    <!-- Stats bar -->
    <div class="grid grid-cols-4 gap-4">
        @foreach([['label'=>'Total','value'=>$total,'color'=>'indigo'],['label'=>'Active','value'=>$activeCount,'color'=>'green'],['label'=>'On Trial','value'=>$trialCount,'color'=>'blue'],['label'=>'Suspended','value'=>$suspendedCount,'color'=>'red']] as $s)
        <div class="bg-white dark:bg-[#0f0f1a] rounded-xl border border-gray-100 dark:border-white/5 px-4 py-3 flex items-center gap-3">
            <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $s['value'] }}</p>
            <p class="text-sm text-gray-500 dark:text-gray-400">{{ $s['label'] }}</p>
        </div>
        @endforeach
    </div>

    <!-- Filters -->
    <form method="GET" action="/superadmin/organizations" class="flex flex-wrap gap-3">
        <input type="text" name="search" value="{{ $search }}" placeholder="Search by name, email, subdomain..."
               class="flex-1 min-w-48 px-3 py-2 rounded-xl border border-gray-200 dark:border-white/10 bg-white dark:bg-[#0f0f1a] text-gray-900 dark:text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 text-sm">
        <select name="status" onchange="this.form.submit()"
                class="px-3 py-2 rounded-xl border border-gray-200 dark:border-white/10 bg-white dark:bg-[#0f0f1a] text-gray-900 dark:text-white text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
            <option value="">All Status</option>
            <option value="active"    {{ $filter === 'active'    ? 'selected' : '' }}>Active</option>
            <option value="trial"     {{ $filter === 'trial'     ? 'selected' : '' }}>Trial</option>
            <option value="inactive"  {{ $filter === 'inactive'  ? 'selected' : '' }}>Inactive</option>
            <option value="suspended" {{ $filter === 'suspended' ? 'selected' : '' }}>Suspended</option>
        </select>
        @if($search)
            <a href="/superadmin/organizations" class="px-3 py-2 rounded-xl border border-gray-200 dark:border-white/10 text-gray-500 text-sm hover:bg-gray-50 dark:hover:bg-white/5 transition-colors">Clear</a>
        @endif
    </form>

    @if(session('success'))
        <div class="px-4 py-3 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-xl text-sm text-green-700 dark:text-green-400">{{ session('success') }}</div>
    @endif

    <!-- Table -->
    <div class="bg-white dark:bg-[#0f0f1a] rounded-2xl border border-gray-100 dark:border-white/5 overflow-hidden">
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b border-gray-100 dark:border-white/5">
                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-400 uppercase tracking-wide">Organization</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-400 uppercase tracking-wide">Contact</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-400 uppercase tracking-wide">Plan</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-400 uppercase tracking-wide">Status</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-400 uppercase tracking-wide">Trial Ends</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-400 uppercase tracking-wide">Joined</th>
                    <th class="px-5 py-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50 dark:divide-white/3">
                @forelse($orgs as $org)
                    @php
                        $sub   = collect($subscriptions)->firstWhere('company_id', $org->id);
                        $plan  = $sub ? collect($plans)->firstWhere('id', $sub->plan_id) : null;
                        if ($org->suspended_at)        $status = 'suspended';
                        elseif (!$org->is_active)       $status = 'inactive';
                        elseif ($sub?->status === 'trial') $status = 'trial';
                        else                            $status = 'active';
                        $statusColor = match($status) {
                            'active'    => 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400',
                            'trial'     => 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400',
                            'suspended' => 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400',
                            default     => 'bg-gray-100 text-gray-600 dark:bg-white/5 dark:text-gray-400',
                        };
                    @endphp
                    <tr class="hover:bg-gray-50 dark:hover:bg-white/2 transition-colors">
                        <td class="px-5 py-3">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-xl bg-indigo-50 dark:bg-indigo-900/20 flex items-center justify-center text-indigo-600 dark:text-indigo-400 text-xs font-bold flex-shrink-0">
                                    {{ strtoupper(substr($org->name, 0, 2)) }}
                                </div>
                                <div>
                                    <p class="font-semibold text-gray-900 dark:text-white">{{ $org->name }}</p>
                                    @if($org->subdomain)<p class="text-xs text-gray-400 font-mono">{{ $org->subdomain }}.app</p>@endif
                                </div>
                            </div>
                        </td>
                        <td class="px-5 py-3">
                            <p class="text-gray-600 dark:text-gray-300 text-xs">{{ $org->email ?: '—' }}</p>
                            <p class="text-gray-400 text-xs">{{ $org->phone ?: '' }}</p>
                        </td>
                        <td class="px-5 py-3 text-gray-500 dark:text-gray-400 text-xs">
                            {{ $plan?->name ?? '—' }}
                            @if($sub) <p class="text-gray-400">${{ $sub->amount }}/{{ $sub->billing_cycle }}</p>@endif
                        </td>
                        <td class="px-5 py-3">
                            <span class="inline-flex px-2 py-0.5 rounded-full text-xs font-medium capitalize {{ $statusColor }}">{{ $status }}</span>
                        </td>
                        <td class="px-5 py-3 text-gray-400 text-xs">
                            @if($org->trial_ends_at)
                                @php $daysLeft = \Carbon\Carbon::now()->diffInDays(\Carbon\Carbon::parse($org->trial_ends_at), false); @endphp
                                <span class="{{ $daysLeft <= 3 ? 'text-red-500' : ($daysLeft <= 7 ? 'text-amber-500' : 'text-gray-400') }}">
                                    {{ \Carbon\Carbon::parse($org->trial_ends_at)->format('M d, Y') }}
                                    @if($daysLeft >= 0) <br>({{ $daysLeft }}d left) @endif
                                </span>
                            @else —
                            @endif
                        </td>
                        <td class="px-5 py-3 text-gray-400 text-xs">{{ \Carbon\Carbon::parse($org->created_at)->format('M d, Y') }}</td>
                        <td class="px-5 py-3 text-right">
                            <div class="flex items-center justify-end gap-1.5">
                                <a href="/superadmin/organizations/{{ $org->id }}"
                                   class="px-2.5 py-1 rounded-lg bg-indigo-50 dark:bg-indigo-900/20 hover:bg-indigo-100 dark:hover:bg-indigo-900/40 text-indigo-600 dark:text-indigo-400 text-xs font-medium transition-colors">
                                    View
                                </a>
                                @if($org->is_active && !$org->suspended_at)
                                    <form method="POST" action="/superadmin/organizations/{{ $org->id }}/suspend">
                                        @csrf
                                        <button type="submit" onclick="return confirm('Suspend {{ addslashes($org->name) }}?')"
                                                class="px-2.5 py-1 rounded-lg bg-red-50 dark:bg-red-900/20 hover:bg-red-100 text-red-600 dark:text-red-400 text-xs font-medium transition-colors">
                                            Suspend
                                        </button>
                                    </form>
                                @elseif($org->suspended_at)
                                    <form method="POST" action="/superadmin/organizations/{{ $org->id }}/unsuspend">
                                        @csrf
                                        <button type="submit"
                                                class="px-2.5 py-1 rounded-lg bg-green-50 dark:bg-green-900/20 hover:bg-green-100 text-green-600 dark:text-green-400 text-xs font-medium transition-colors">
                                            Unsuspend
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="px-5 py-8 text-center text-gray-400">No organizations found.</td></tr>
                @endforelse
            </tbody>
        </table>
        @if($orgs->hasPages())
            <div class="px-5 py-3 border-t border-gray-100 dark:border-white/5">{{ $orgs->links() }}</div>
        @endif
    </div>
@endsection

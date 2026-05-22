@extends('superadmin.layouts.app')
@section('title', $org->name)
@section('breadcrumb', 'Organizations')

@section('content')
    <div class="flex items-center gap-3 mb-5">
        <a href="/superadmin/organizations" class="p-2 rounded-xl text-gray-400 hover:text-gray-600 hover:bg-gray-100 dark:hover:bg-white/5 transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
        </a>
        <div class="w-10 h-10 rounded-xl bg-indigo-50 dark:bg-indigo-900/20 flex items-center justify-center text-indigo-600 dark:text-indigo-400 font-bold">
            {{ strtoupper(substr($org->name, 0, 2)) }}
        </div>
        <div>
            <h1 class="text-xl font-bold text-gray-900 dark:text-white">{{ $org->name }}</h1>
            @if($org->subdomain)<p class="text-sm text-gray-400 font-mono">{{ $org->subdomain }}.app</p>@endif
        </div>
        <div class="ml-auto flex items-center gap-2">
            @php
                if ($org->suspended_at)     $status = 'suspended';
                elseif (!$org->is_active)   $status = 'inactive';
                elseif ($sub?->status === 'trial') $status = 'trial';
                else                        $status = 'active';
                $statusColor = match($status) {
                    'active'    => 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400',
                    'trial'     => 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400',
                    'suspended' => 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400',
                    default     => 'bg-gray-100 text-gray-600 dark:bg-white/5 dark:text-gray-400',
                };
            @endphp
            <span class="inline-flex px-3 py-1 rounded-full text-sm font-medium capitalize {{ $statusColor }}">{{ $status }}</span>
        </div>
    </div>

    @if(session('success'))
        <div class="px-4 py-3 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-xl text-sm text-green-700 dark:text-green-400 mb-5">{{ session('success') }}</div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">
        <!-- Details -->
        <div class="lg:col-span-2 space-y-5">
            <div class="bg-white dark:bg-[#0f0f1a] rounded-2xl border border-gray-100 dark:border-white/5 p-5">
                <h2 class="font-semibold text-gray-900 dark:text-white text-sm mb-4">Organization Details</h2>
                <div class="grid grid-cols-2 gap-4 text-sm">
                    <div><p class="text-xs text-gray-400 mb-1">Name</p><p class="font-medium text-gray-900 dark:text-white">{{ $org->name }}</p></div>
                    <div><p class="text-xs text-gray-400 mb-1">Subdomain</p><p class="font-medium font-mono text-gray-900 dark:text-white">{{ $org->subdomain ?: '—' }}</p></div>
                    <div><p class="text-xs text-gray-400 mb-1">Email</p><p class="font-medium text-gray-900 dark:text-white">{{ $org->email ?: '—' }}</p></div>
                    <div><p class="text-xs text-gray-400 mb-1">Phone</p><p class="font-medium text-gray-900 dark:text-white">{{ $org->phone ?: '—' }}</p></div>
                    <div><p class="text-xs text-gray-400 mb-1">Website</p><p class="font-medium text-gray-900 dark:text-white">{{ $org->website ?: '—' }}</p></div>
                    <div><p class="text-xs text-gray-400 mb-1">Tax ID</p><p class="font-medium text-gray-900 dark:text-white">{{ $org->tax_id ?: '—' }}</p></div>
                    <div><p class="text-xs text-gray-400 mb-1">City</p><p class="font-medium text-gray-900 dark:text-white">{{ $org->city ?: '—' }}</p></div>
                    <div><p class="text-xs text-gray-400 mb-1">Founded</p><p class="font-medium text-gray-900 dark:text-white">{{ $org->founded_date ? \Carbon\Carbon::parse($org->founded_date)->format('M d, Y') : '—' }}</p></div>
                    <div><p class="text-xs text-gray-400 mb-1">Registered</p><p class="font-medium text-gray-900 dark:text-white">{{ \Carbon\Carbon::parse($org->created_at)->format('M d, Y H:i') }}</p></div>
                    <div><p class="text-xs text-gray-400 mb-1">Tenant</p><p class="font-medium text-gray-900 dark:text-white">{{ $org->is_tenant ? 'Yes' : 'No' }}</p></div>
                </div>
            </div>

            <!-- Subscription -->
            <div class="bg-white dark:bg-[#0f0f1a] rounded-2xl border border-gray-100 dark:border-white/5 p-5">
                <h2 class="font-semibold text-gray-900 dark:text-white text-sm mb-4">Subscription</h2>
                @if($sub)
                    <div class="grid grid-cols-2 gap-4 text-sm">
                        <div><p class="text-xs text-gray-400 mb-1">Plan</p><p class="font-semibold text-gray-900 dark:text-white">{{ $plan?->name ?? 'Unknown' }}</p></div>
                        <div><p class="text-xs text-gray-400 mb-1">Status</p>
                            <span class="inline-flex px-2 py-0.5 rounded-full text-xs font-medium capitalize {{ $statusColor }}">{{ $sub->status }}</span>
                        </div>
                        <div><p class="text-xs text-gray-400 mb-1">Amount</p><p class="font-medium text-gray-900 dark:text-white">${{ $sub->amount }} / {{ $sub->billing_cycle }}</p></div>
                        <div><p class="text-xs text-gray-400 mb-1">Currency</p><p class="font-medium text-gray-900 dark:text-white">{{ $sub->currency }}</p></div>
                        <div><p class="text-xs text-gray-400 mb-1">Starts At</p><p class="font-medium text-gray-900 dark:text-white">{{ $sub->starts_at ? \Carbon\Carbon::parse($sub->starts_at)->format('M d, Y') : '—' }}</p></div>
                        <div><p class="text-xs text-gray-400 mb-1">Ends At</p><p class="font-medium text-gray-900 dark:text-white">{{ $sub->ends_at ? \Carbon\Carbon::parse($sub->ends_at)->format('M d, Y') : 'Ongoing' }}</p></div>
                        @if($sub->trial_ends_at)
                            @php $daysLeft = \Carbon\Carbon::now()->diffInDays(\Carbon\Carbon::parse($sub->trial_ends_at), false); @endphp
                            <div class="col-span-2"><p class="text-xs text-gray-400 mb-1">Trial Ends</p>
                                <p class="font-medium {{ $daysLeft <= 3 ? 'text-red-500' : ($daysLeft <= 7 ? 'text-amber-500' : 'text-gray-900 dark:text-white') }}">
                                    {{ \Carbon\Carbon::parse($sub->trial_ends_at)->format('M d, Y') }}
                                    @if($daysLeft >= 0) ({{ $daysLeft }} days left) @else (expired) @endif
                                </p>
                            </div>
                        @endif
                    </div>
                @else
                    <p class="text-sm text-gray-400">No active subscription.</p>
                @endif
            </div>
        </div>

        <!-- Actions panel -->
        <div class="space-y-4">
            <div class="bg-white dark:bg-[#0f0f1a] rounded-2xl border border-gray-100 dark:border-white/5 p-5">
                <h2 class="font-semibold text-gray-900 dark:text-white text-sm mb-3">Actions</h2>
                <div class="space-y-2">
                    @if($org->is_active && !$org->suspended_at)
                        <form method="POST" action="/superadmin/organizations/{{ $org->id }}/suspend">
                            @csrf
                            <div class="mb-2">
                                <input type="text" name="reason" placeholder="Suspension reason (optional)"
                                       class="w-full px-3 py-2 rounded-xl border border-gray-200 dark:border-white/10 bg-white dark:bg-[#1a1a2e] text-gray-900 dark:text-white text-xs focus:outline-none focus:ring-2 focus:ring-red-500">
                            </div>
                            <button type="submit" onclick="return confirm('Suspend this organization?')"
                                    class="w-full px-3 py-2 bg-red-50 dark:bg-red-900/20 hover:bg-red-100 text-red-600 dark:text-red-400 rounded-xl text-sm font-medium transition-colors">
                                Suspend Organization
                            </button>
                        </form>
                    @elseif($org->suspended_at)
                        <form method="POST" action="/superadmin/organizations/{{ $org->id }}/unsuspend">
                            @csrf
                            <button type="submit"
                                    class="w-full px-3 py-2 bg-green-50 dark:bg-green-900/20 hover:bg-green-100 text-green-600 dark:text-green-400 rounded-xl text-sm font-medium transition-colors">
                                Restore / Unsuspend
                            </button>
                        </form>
                        @if($org->suspension_reason)
                            <p class="text-xs text-red-400 px-1">Reason: {{ $org->suspension_reason }}</p>
                        @endif
                    @endif

                    @if(!$org->is_active)
                        <form method="POST" action="/superadmin/organizations/{{ $org->id }}/activate">
                            @csrf
                            <button type="submit"
                                    class="w-full px-3 py-2 bg-indigo-50 dark:bg-indigo-900/20 hover:bg-indigo-100 text-indigo-600 dark:text-indigo-400 rounded-xl text-sm font-medium transition-colors">
                                Activate Organization
                            </button>
                        </form>
                    @endif

                    <a href="/superadmin/organizations" class="block text-center w-full px-3 py-2 bg-gray-50 dark:bg-white/3 hover:bg-gray-100 dark:hover:bg-white/5 text-gray-600 dark:text-gray-400 rounded-xl text-sm font-medium transition-colors">
                        ← Back to Organizations
                    </a>
                </div>
            </div>

            <!-- Plan info -->
            @if($plan)
            <div class="bg-indigo-50 dark:bg-indigo-900/10 border border-indigo-100 dark:border-indigo-900/30 rounded-2xl p-4">
                <p class="text-xs font-semibold text-indigo-500 uppercase tracking-wide mb-2">Current Plan</p>
                <p class="font-bold text-gray-900 dark:text-white">{{ $plan->name }}</p>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ $plan->description }}</p>
                <div class="mt-3 space-y-1">
                    <p class="text-xs text-gray-500">Up to {{ $plan->max_users }} users</p>
                    @if($plan->features)
                        @foreach(json_decode($plan->features, true) ?? [] as $feat)
                            <p class="text-xs text-gray-500">✓ {{ $feat['value'] }}</p>
                        @endforeach
                    @endif
                </div>
            </div>
            @endif
        </div>
    </div>
@endsection

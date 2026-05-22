@extends('superadmin.layouts.app')
@section('title', 'Plans')
@section('breadcrumb', 'Plans')

@section('content')
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-xl font-bold text-gray-900 dark:text-white">Subscription Plans</h1>
            <p class="text-sm text-gray-500 mt-1">{{ count($plans) }} plans available on the platform</p>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-5">
        @foreach($plans as $plan)
            @php $count = $planCounts[$plan->id] ?? 0; @endphp
            <div class="bg-white dark:bg-[#0f0f1a] rounded-2xl border border-gray-100 dark:border-white/5 p-5">
                <div class="flex items-start justify-between mb-3">
                    <div>
                        <h3 class="font-bold text-gray-900 dark:text-white">{{ $plan->name }}</h3>
                        @if($plan->description)<p class="text-xs text-gray-400 mt-0.5">{{ $plan->description }}</p>@endif
                    </div>
                    <span class="inline-flex px-2 py-0.5 rounded-full text-xs font-medium {{ $plan->is_active ? 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400' : 'bg-gray-100 text-gray-500 dark:bg-white/5 dark:text-gray-400' }}">
                        {{ $plan->is_active ? 'Active' : 'Inactive' }}
                    </span>
                </div>

                <div class="space-y-1 mb-4">
                    <div class="flex items-baseline gap-1.5">
                        <span class="text-2xl font-extrabold text-gray-900 dark:text-white">${{ $plan->price_monthly }}</span>
                        <span class="text-xs text-gray-400">/mo</span>
                        @if($plan->price_yearly)
                            <span class="text-xs text-gray-400 ml-1">&bull; ${{ $plan->price_yearly }}/yr</span>
                        @endif
                    </div>
                    <p class="text-xs text-gray-400">Up to {{ $plan->max_users }} users</p>
                </div>

                <div class="flex items-center justify-between py-3 border-t border-gray-50 dark:border-white/5">
                    <p class="text-sm font-semibold text-indigo-600 dark:text-indigo-400">{{ $count }} subscriber{{ $count !== 1 ? 's' : '' }}</p>
                </div>

                @if($plan->features)
                    @php $feats = json_decode($plan->features, true) ?? []; @endphp
                    @if($feats)
                        <ul class="space-y-1 mt-2">
                            @foreach($feats as $feat)
                                <li class="flex items-center gap-1.5 text-xs text-gray-500 dark:text-gray-400">
                                    <svg class="w-3.5 h-3.5 text-green-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                    {{ $feat['value'] }}
                                </li>
                            @endforeach
                        </ul>
                    @endif
                @endif

                @if($plan->allowed_plugins)
                    @php $plugins = is_string($plan->allowed_plugins) ? json_decode($plan->allowed_plugins, true) : $plan->allowed_plugins; @endphp
                    @if($plugins)
                        <p class="text-xs text-gray-400 mt-3">Plugins: {{ implode(', ', $plugins) }}</p>
                    @endif
                @endif
            </div>
        @endforeach

        @if(empty($plans))
            <div class="col-span-3 py-16 text-center text-gray-400">
                <p>No subscription plans defined yet.</p>
                <p class="text-sm mt-1">Run the database seeder to add plans.</p>
            </div>
        @endif
    </div>
@endsection

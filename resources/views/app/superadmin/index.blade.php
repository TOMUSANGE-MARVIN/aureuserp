@extends('app.layouts.app')
@section('title', 'Super Admin')

@section('content')
<div class="space-y-6">
    <div>
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Super Admin</h1>
        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Global tenant management across the platform</p>
    </div>

    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        @foreach([
            ['label' => 'Total Tenants', 'value' => $stats['tenants']],
            ['label' => 'Active', 'value' => $stats['active']],
            ['label' => 'In Trial', 'value' => $stats['trial']],
            ['label' => 'Suspended', 'value' => $stats['suspended']],
        ] as $card)
        <div class="bg-white dark:bg-gray-800 rounded-xl p-4 border border-gray-200 dark:border-gray-700 shadow-sm">
            <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide">{{ $card['label'] }}</p>
            <p class="text-2xl font-bold text-gray-900 dark:text-white mt-1">{{ $card['value'] }}</p>
        </div>
        @endforeach
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm p-6">
        <a href="/superadmin/tenants" class="inline-flex items-center gap-2 px-4 py-2 bg-brand-600 hover:bg-brand-700 text-white rounded-lg text-sm font-medium">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8h2a2 2 0 012 2v10a2 2 0 01-2 2H5a2 2 0 01-2-2V10a2 2 0 012-2h2m10-4H7m10 0v4H7V4m10 0a2 2 0 012 2v0a2 2 0 01-2 2M7 4a2 2 0 00-2 2v0a2 2 0 002 2"/></svg>
            Open Tenant Directory
        </a>
    </div>
</div>
@endsection

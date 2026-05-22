@extends('app.layouts.app')
@section('title', 'Work Orders')

@section('content')
<div class="space-y-6">
    <div>
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Work Orders</h1>
        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Individual work operations</p>
    </div>

    <div class="flex gap-2 border-b border-gray-200 dark:border-gray-700">
        <a href="/app/manufacturing" class="px-4 py-2 text-sm font-medium text-gray-500 dark:text-gray-400 hover:text-gray-700">Orders</a>
        <a href="/app/manufacturing/work-orders" class="px-4 py-2 text-sm font-medium text-brand-600 border-b-2 border-brand-600">Work Orders</a>
        <a href="/app/manufacturing/bom" class="px-4 py-2 text-sm font-medium text-gray-500 dark:text-gray-400 hover:text-gray-700">Bills of Materials</a>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 dark:bg-gray-700/50">
                <tr>
                    <th class="px-4 py-3 text-left font-semibold text-gray-600 dark:text-gray-300">Name</th>
                    <th class="px-4 py-3 text-left font-semibold text-gray-600 dark:text-gray-300">Product</th>
                    <th class="px-4 py-3 text-left font-semibold text-gray-600 dark:text-gray-300">Work Center</th>
                    <th class="px-4 py-3 text-left font-semibold text-gray-600 dark:text-gray-300">MO</th>
                    <th class="px-4 py-3 text-left font-semibold text-gray-600 dark:text-gray-300">State</th>
                    <th class="px-4 py-3 text-left font-semibold text-gray-600 dark:text-gray-300">Duration</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                @forelse($orders as $wo)
                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/30 transition-colors">
                    <td class="px-4 py-3 font-medium text-gray-900 dark:text-white">{{ $wo->name }}</td>
                    <td class="px-4 py-3 text-gray-600 dark:text-gray-300">{{ $wo->product_name ?? '—' }}</td>
                    <td class="px-4 py-3 text-gray-600 dark:text-gray-300">{{ $wo->work_center_name ?? '—' }}</td>
                    <td class="px-4 py-3"><a href="/app/manufacturing/{{ $wo->manufacturing_order_id }}" class="text-brand-600 hover:underline text-xs">{{ $wo->mo_name ?? '#'.$wo->manufacturing_order_id }}</a></td>
                    <td class="px-4 py-3"><span class="px-2 py-0.5 rounded-full text-xs bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 capitalize">{{ $wo->state }}</span></td>
                    <td class="px-4 py-3 text-gray-500 dark:text-gray-400 text-xs">{{ $wo->duration ? round($wo->duration).' min' : '—' }}</td>
                </tr>
                @empty
                <tr><td colspan="6" class="px-4 py-10 text-center text-gray-500 dark:text-gray-400">No work orders found.</td></tr>
                @endforelse
            </tbody>
        </table>
        @if($orders->hasPages())
        <div class="px-4 py-3 border-t border-gray-100 dark:border-gray-700">{{ $orders->links() }}</div>
        @endif
    </div>
</div>
@endsection

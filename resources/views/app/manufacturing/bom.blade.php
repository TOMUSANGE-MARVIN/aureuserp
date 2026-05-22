@extends('app.layouts.app')
@section('title', 'Bills of Materials')

@section('content')
<div class="space-y-6">
    <div>
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Bills of Materials</h1>
        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Product component definitions</p>
    </div>

    <div class="flex gap-2 border-b border-gray-200 dark:border-gray-700">
        <a href="/app/manufacturing" class="px-4 py-2 text-sm font-medium text-gray-500 dark:text-gray-400 hover:text-gray-700">Orders</a>
        <a href="/app/manufacturing/work-orders" class="px-4 py-2 text-sm font-medium text-gray-500 dark:text-gray-400 hover:text-gray-700">Work Orders</a>
        <a href="/app/manufacturing/bom" class="px-4 py-2 text-sm font-medium text-brand-600 border-b-2 border-brand-600">Bills of Materials</a>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 dark:bg-gray-700/50">
                <tr>
                    <th class="px-4 py-3 text-left font-semibold text-gray-600 dark:text-gray-300">Code</th>
                    <th class="px-4 py-3 text-left font-semibold text-gray-600 dark:text-gray-300">Product</th>
                    <th class="px-4 py-3 text-left font-semibold text-gray-600 dark:text-gray-300">Type</th>
                    <th class="px-4 py-3 text-left font-semibold text-gray-600 dark:text-gray-300">Quantity</th>
                    <th class="px-4 py-3 text-left font-semibold text-gray-600 dark:text-gray-300">Status</th>
                    <th class="px-4 py-3 text-left font-semibold text-gray-600 dark:text-gray-300">Created</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                @forelse($boms as $bom)
                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/30 transition-colors">
                    <td class="px-4 py-3 font-medium text-gray-900 dark:text-white">{{ $bom->code ?? '—' }}</td>
                    <td class="px-4 py-3 text-gray-600 dark:text-gray-300">{{ $bom->product_name ?? '—' }}</td>
                    <td class="px-4 py-3 text-gray-600 dark:text-gray-300 capitalize">{{ $bom->type ?? '—' }}</td>
                    <td class="px-4 py-3 text-gray-600 dark:text-gray-300">{{ $bom->quantity ?? 1 }}</td>
                    <td class="px-4 py-3">
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium {{ $bom->ready_to_produce ? 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400' : 'bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300' }}">
                            {{ $bom->ready_to_produce ? 'Ready' : 'Not Ready' }}
                        </span>
                    </td>
                    <td class="px-4 py-3 text-gray-500 dark:text-gray-400 text-xs">{{ $bom->created_at ? date('d M Y', strtotime($bom->created_at)) : '—' }}</td>
                </tr>
                @empty
                <tr><td colspan="6" class="px-4 py-10 text-center text-gray-500 dark:text-gray-400">No bills of materials found.</td></tr>
                @endforelse
            </tbody>
        </table>
        @if($boms->hasPages())
        <div class="px-4 py-3 border-t border-gray-100 dark:border-gray-700">{{ $boms->links() }}</div>
        @endif
    </div>
</div>
@endsection

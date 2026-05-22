@extends('app.layouts.app')
@section('title', 'Manufacturing Orders')

@section('content')
<div class="space-y-6">
    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Manufacturing Orders</h1>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Manage production orders</p>
        </div>
    </div>

    {{-- Stats --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        @foreach([
            ['label'=>'Total','value'=>$stats['total'],'color'=>'brand'],
            ['label'=>'Draft','value'=>$stats['draft'],'color'=>'gray'],
            ['label'=>'In Progress','value'=>$stats['in_progress'],'color'=>'yellow'],
            ['label'=>'Done','value'=>$stats['done'],'color'=>'green'],
        ] as $s)
        <div class="bg-white dark:bg-gray-800 rounded-xl p-4 border border-gray-200 dark:border-gray-700 shadow-sm">
            <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide">{{ $s['label'] }}</p>
            <p class="text-2xl font-bold text-gray-900 dark:text-white mt-1">{{ $s['value'] }}</p>
        </div>
        @endforeach
    </div>

    {{-- Sub-navigation --}}
    <div class="flex gap-2 border-b border-gray-200 dark:border-gray-700">
        <a href="/app/manufacturing" class="px-4 py-2 text-sm font-medium text-brand-600 border-b-2 border-brand-600">Orders</a>
        <a href="/app/manufacturing/work-orders" class="px-4 py-2 text-sm font-medium text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200">Work Orders</a>
        <a href="/app/manufacturing/bom" class="px-4 py-2 text-sm font-medium text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200">Bills of Materials</a>
    </div>

    {{-- Search + Filter --}}
    <form method="GET" action="/app/manufacturing" class="flex flex-col sm:flex-row gap-3">
        <input type="text" name="search" value="{{ $search }}" placeholder="Search orders…"
            class="flex-1 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-white px-4 py-2 text-sm focus:ring-2 focus:ring-brand-500 focus:border-transparent">
        <select name="filter" class="rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-white px-4 py-2 text-sm">
            <option value="all" {{ $filter=='all'?'selected':'' }}>All States</option>
            <option value="draft" {{ $filter=='draft'?'selected':'' }}>Draft</option>
            <option value="confirmed" {{ $filter=='confirmed'?'selected':'' }}>Confirmed</option>
            <option value="in_progress" {{ $filter=='in_progress'?'selected':'' }}>In Progress</option>
            <option value="done" {{ $filter=='done'?'selected':'' }}>Done</option>
            <option value="cancel" {{ $filter=='cancel'?'selected':'' }}>Cancelled</option>
        </select>
        <button type="submit" class="px-4 py-2 bg-brand-600 hover:bg-brand-700 text-white rounded-lg text-sm font-medium">Filter</button>
    </form>

    {{-- Table --}}
    <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 dark:bg-gray-700/50">
                <tr>
                    <th class="px-4 py-3 text-left font-semibold text-gray-600 dark:text-gray-300">Reference</th>
                    <th class="px-4 py-3 text-left font-semibold text-gray-600 dark:text-gray-300">Product</th>
                    <th class="px-4 py-3 text-left font-semibold text-gray-600 dark:text-gray-300">Qty</th>
                    <th class="px-4 py-3 text-left font-semibold text-gray-600 dark:text-gray-300">State</th>
                    <th class="px-4 py-3 text-left font-semibold text-gray-600 dark:text-gray-300">Priority</th>
                    <th class="px-4 py-3 text-left font-semibold text-gray-600 dark:text-gray-300">Date</th>
                    <th class="px-4 py-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                @forelse($orders as $o)
                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/30 transition-colors">
                    <td class="px-4 py-3 font-medium text-gray-900 dark:text-white">{{ $o->name ?? 'MO-'.$o->id }}</td>
                    <td class="px-4 py-3 text-gray-600 dark:text-gray-300">{{ $o->product_name ?? '—' }}</td>
                    <td class="px-4 py-3 text-gray-600 dark:text-gray-300">{{ $o->quantity ?? 0 }}</td>
                    <td class="px-4 py-3">
                        @php $colors=['draft'=>'gray','confirmed'=>'blue','in_progress'=>'yellow','done'=>'green','cancel'=>'red']; $c=$colors[$o->state]??'gray'; @endphp
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-{{ $c }}-100 text-{{ $c }}-700 dark:bg-{{ $c }}-900/30 dark:text-{{ $c }}-400 capitalize">{{ str_replace('_',' ',$o->state) }}</span>
                    </td>
                    <td class="px-4 py-3">
                        @for($i=0;$i<($o->priority??0);$i++)<span class="text-yellow-400">★</span>@endfor
                    </td>
                    <td class="px-4 py-3 text-gray-500 dark:text-gray-400 text-xs">{{ $o->scheduled_date ? date('d M Y', strtotime($o->scheduled_date)) : '—' }}</td>
                    <td class="px-4 py-3 text-right">
                        <a href="/app/manufacturing/{{ $o->id }}" class="text-brand-600 hover:text-brand-700 font-medium text-xs">View</a>
                    </td>
                </tr>
                @empty
                <tr><td colspan="7" class="px-4 py-10 text-center text-gray-500 dark:text-gray-400">No manufacturing orders found.</td></tr>
                @endforelse
            </tbody>
        </table>
        @if($orders->hasPages())
        <div class="px-4 py-3 border-t border-gray-100 dark:border-gray-700">{{ $orders->links() }}</div>
        @endif
    </div>
</div>
@endsection

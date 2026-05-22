@extends('app.layouts.app')
@section('title', 'Manufacturing Order')

@section('content')
<div class="space-y-6">
    <div class="flex items-center gap-4">
        <a href="/app/manufacturing" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
        </a>
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">{{ $order->name ?? 'MO-'.$order->id }}</h1>
        @php $colors=['draft'=>'gray','confirmed'=>'blue','in_progress'=>'yellow','done'=>'green','cancel'=>'red']; $c=$colors[$order->state]??'gray'; @endphp
        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-{{ $c }}-100 text-{{ $c }}-700 dark:bg-{{ $c }}-900/30 dark:text-{{ $c }}-400 capitalize">{{ str_replace('_',' ',$order->state) }}</span>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm p-6">
                <h2 class="text-base font-semibold text-gray-900 dark:text-white mb-4">Order Details</h2>
                <dl class="grid grid-cols-2 gap-4 text-sm">
                    <div><dt class="text-gray-500 dark:text-gray-400">Product</dt><dd class="font-medium text-gray-900 dark:text-white mt-1">{{ $order->product_name ?? '—' }}</dd></div>
                    <div><dt class="text-gray-500 dark:text-gray-400">Quantity</dt><dd class="font-medium text-gray-900 dark:text-white mt-1">{{ $order->quantity ?? 0 }}</dd></div>
                    <div><dt class="text-gray-500 dark:text-gray-400">Scheduled Date</dt><dd class="font-medium text-gray-900 dark:text-white mt-1">{{ $order->scheduled_date ? date('d M Y', strtotime($order->scheduled_date)) : '—' }}</dd></div>
                    <div><dt class="text-gray-500 dark:text-gray-400">Priority</dt><dd class="font-medium text-gray-900 dark:text-white mt-1">@for($i=0;$i<($order->priority??0);$i++)<span class="text-yellow-400">★</span>@endfor{{ ($order->priority??0)==0?'Normal':'' }}</dd></div>
                </dl>
            </div>

            @if($workOrders->count())
            <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-700">
                    <h2 class="text-base font-semibold text-gray-900 dark:text-white">Work Orders</h2>
                </div>
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 dark:bg-gray-700/50">
                        <tr>
                            <th class="px-4 py-3 text-left font-semibold text-gray-600 dark:text-gray-300">Name</th>
                            <th class="px-4 py-3 text-left font-semibold text-gray-600 dark:text-gray-300">Work Center</th>
                            <th class="px-4 py-3 text-left font-semibold text-gray-600 dark:text-gray-300">State</th>
                            <th class="px-4 py-3 text-left font-semibold text-gray-600 dark:text-gray-300">Duration</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                        @foreach($workOrders as $wo)
                        <tr>
                            <td class="px-4 py-3 text-gray-900 dark:text-white">{{ $wo->name }}</td>
                            <td class="px-4 py-3 text-gray-600 dark:text-gray-300">{{ $wo->work_center_name ?? '—' }}</td>
                            <td class="px-4 py-3"><span class="px-2 py-0.5 rounded-full text-xs bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 capitalize">{{ $wo->state }}</span></td>
                            <td class="px-4 py-3 text-gray-600 dark:text-gray-300">{{ $wo->duration ? round($wo->duration).' min' : '—' }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @endif
        </div>

        <div class="space-y-4">
            <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm p-6">
                <h2 class="text-base font-semibold text-gray-900 dark:text-white mb-4">Timeline</h2>
                <dl class="space-y-3 text-sm">
                    <div><dt class="text-gray-500 dark:text-gray-400">Created</dt><dd class="font-medium text-gray-900 dark:text-white">{{ $order->created_at ? date('d M Y', strtotime($order->created_at)) : '—' }}</dd></div>
                    <div><dt class="text-gray-500 dark:text-gray-400">Updated</dt><dd class="font-medium text-gray-900 dark:text-white">{{ $order->updated_at ? date('d M Y', strtotime($order->updated_at)) : '—' }}</dd></div>
                </dl>
            </div>
        </div>
    </div>
</div>
@endsection

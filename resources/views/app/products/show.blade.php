@extends('app.layouts.app')
@section('title', $product->name . ' — Product')
@section('breadcrumb', 'Products')

@section('topbar_actions')
    <a href="{{ route('products.edit', $product->id) }}" class="px-4 py-2 bg-brand-600 hover:bg-brand-700 text-white rounded-xl text-sm font-medium transition-colors">Edit</a>
    <form method="POST" action="{{ route('products.destroy', $product->id) }}" class="inline">
        @csrf @method('DELETE')
        <button type="submit" onclick="return confirm('Delete this product?')"
                class="px-4 py-2 bg-red-500 hover:bg-red-600 text-white rounded-xl text-sm font-medium transition-colors">Delete</button>
    </form>
@endsection

@section('content')
    @if(session('success'))
        <div class="flex items-center gap-3 px-4 py-3 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-xl text-green-700 dark:text-green-400 text-sm">
            <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
            {{ session('success') }}
        </div>
    @endif

    @php
        $typeMap = ['consu' => ['label'=>'Consumable','class'=>'bg-orange-100 dark:bg-orange-900/30 text-orange-700 dark:text-orange-400'],
                    'service' => ['label'=>'Service','class'=>'bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-400'],
                    'storable' => ['label'=>'Storable','class'=>'bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400'],
                    'combo' => ['label'=>'Combo','class'=>'bg-brand-100 dark:bg-brand-900/30 text-brand-700 dark:text-brand-400']];
        $typeInfo = $typeMap[$product->type] ?? ['label'=>ucfirst($product->type ?? ''),'class'=>'bg-gray-100 dark:bg-white/10 text-gray-600 dark:text-gray-400'];
    @endphp

    {{-- Header --}}
    <div class="bg-white dark:bg-[#111118] rounded-2xl shadow-sm border border-gray-100 dark:border-white/5 p-6">
        <div class="flex items-start gap-5">
            <div class="w-16 h-16 rounded-2xl bg-brand-100 dark:bg-brand-900/30 flex items-center justify-center flex-shrink-0">
                <svg class="w-8 h-8 text-brand-600 dark:text-brand-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                </svg>
            </div>
            <div class="flex-1 min-w-0">
                <h1 class="text-xl font-bold text-gray-900 dark:text-white">{{ $product->name }}</h1>
                @if($product->reference)
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">Ref: {{ $product->reference }}</p>
                @endif
                <div class="flex flex-wrap gap-2 mt-2">
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $typeInfo['class'] }}">{{ $typeInfo['label'] }}</span>
                    @if($product->sales_ok)
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400">For Sale</span>
                    @endif
                    @if($product->purchase_ok)
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-400">For Purchase</span>
                    @endif
                </div>
            </div>
            @if($product->price !== null)
                <div class="text-right flex-shrink-0">
                    <div class="text-2xl font-bold text-gray-900 dark:text-white">{{ number_format($product->price, 2) }}</div>
                    <div class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Sale Price</div>
                </div>
            @endif
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">
        {{-- Basic Info --}}
        <div class="bg-white dark:bg-[#111118] rounded-2xl shadow-sm border border-gray-100 dark:border-white/5 p-5">
            <h2 class="text-sm font-semibold text-gray-900 dark:text-white mb-4">Basic Information</h2>
            <dl class="space-y-3">
                @foreach([
                    'Reference' => $product->reference,
                    'Barcode' => $product->barcode,
                    'Sale Price' => $product->price !== null ? number_format($product->price, 2) : null,
                    'Cost' => $product->cost !== null ? number_format($product->cost, 2) : null,
                    'Weight' => $product->weight,
                    'Volume' => $product->volume,
                    'Category' => $category?->name,
                ] as $label => $value)
                    <div class="flex items-start gap-3">
                        <dt class="w-28 text-xs text-gray-500 dark:text-gray-400 pt-0.5 flex-shrink-0">{{ $label }}</dt>
                        <dd class="text-sm text-gray-900 dark:text-white">{{ $value ?: '—' }}</dd>
                    </div>
                @endforeach
            </dl>
        </div>

        {{-- Sales & Purchase Info --}}
        <div class="bg-white dark:bg-[#111118] rounded-2xl shadow-sm border border-gray-100 dark:border-white/5 p-5">
            <h2 class="text-sm font-semibold text-gray-900 dark:text-white mb-4">Sales Information</h2>
            <dl class="space-y-3">
                <div class="flex items-start gap-3">
                    <dt class="w-28 text-xs text-gray-500 dark:text-gray-400 pt-0.5 flex-shrink-0">Can be Sold</dt>
                    <dd class="text-sm">
                        @if($product->sales_ok)
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400">Yes</span>
                        @else
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 dark:bg-white/10 text-gray-600 dark:text-gray-400">No</span>
                        @endif
                    </dd>
                </div>
                @if($product->description_sale)
                    <div class="flex items-start gap-3">
                        <dt class="w-28 text-xs text-gray-500 dark:text-gray-400 pt-0.5 flex-shrink-0">Sales Desc.</dt>
                        <dd class="text-sm text-gray-900 dark:text-white">{{ $product->description_sale }}</dd>
                    </div>
                @endif
            </dl>

            <h2 class="text-sm font-semibold text-gray-900 dark:text-white mb-4 mt-6">Purchase Information</h2>
            <dl class="space-y-3">
                <div class="flex items-start gap-3">
                    <dt class="w-28 text-xs text-gray-500 dark:text-gray-400 pt-0.5 flex-shrink-0">Can be Purchased</dt>
                    <dd class="text-sm">
                        @if($product->purchase_ok)
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400">Yes</span>
                        @else
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 dark:bg-white/10 text-gray-600 dark:text-gray-400">No</span>
                        @endif
                    </dd>
                </div>
                @if(isset($product->description_purchase) && $product->description_purchase)
                    <div class="flex items-start gap-3">
                        <dt class="w-28 text-xs text-gray-500 dark:text-gray-400 pt-0.5 flex-shrink-0">Purchase Desc.</dt>
                        <dd class="text-sm text-gray-900 dark:text-white">{{ $product->description_purchase }}</dd>
                    </div>
                @endif
            </dl>
        </div>
    </div>

    @if($product->description)
        <div class="bg-white dark:bg-[#111118] rounded-2xl shadow-sm border border-gray-100 dark:border-white/5 p-5">
            <h2 class="text-sm font-semibold text-gray-900 dark:text-white mb-3">Description</h2>
            <p class="text-sm text-gray-700 dark:text-gray-300 leading-relaxed">{{ $product->description }}</p>
        </div>
    @endif

    <div class="flex gap-3">
        <a href="{{ route('products.index') }}" class="px-4 py-2 bg-gray-100 dark:bg-white/5 hover:bg-gray-200 dark:hover:bg-white/10 text-gray-700 dark:text-gray-300 rounded-xl text-sm font-medium transition-colors">← Back to Products</a>
        <a href="{{ route('products.edit', $product->id) }}" class="px-4 py-2 bg-brand-600 hover:bg-brand-700 text-white rounded-xl text-sm font-medium transition-colors">Edit Product</a>
    </div>
@endsection

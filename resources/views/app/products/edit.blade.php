@extends('app.layouts.app')
@section('title', 'Edit ' . $product->name)
@section('breadcrumb', 'Edit Product')

@section('content')
    <div class="max-w-2xl">
        <div class="flex items-center justify-between mb-5">
            <h1 class="text-xl font-bold text-gray-900 dark:text-white">Edit: {{ $product->name }}</h1>
            <a href="{{ route('products.show', $product->id) }}" class="px-4 py-2 bg-gray-100 dark:bg-white/5 hover:bg-gray-200 dark:hover:bg-white/10 text-gray-700 dark:text-gray-300 rounded-xl text-sm font-medium transition-colors">Cancel</a>
        </div>

        @if($errors->any())
            <div class="mb-5 px-4 py-3 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-xl">
                <ul class="list-disc list-inside text-sm text-red-700 dark:text-red-400 space-y-1">
                    @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('products.update', $product->id) }}" class="space-y-5">
            @csrf
            @method('PUT')

            <div class="bg-white dark:bg-[#111118] rounded-2xl shadow-sm border border-gray-100 dark:border-white/5 p-5 space-y-4">
                <h2 class="text-sm font-semibold text-gray-900 dark:text-white">Basic Information</h2>

                <div>
                    <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1.5">Product Name <span class="text-red-500">*</span></label>
                    <input type="text" name="name" value="{{ old('name', $product->name) }}" required
                           class="w-full px-3 py-2 rounded-xl border border-gray-200 dark:border-white/10 bg-white dark:bg-[#1a1a27] text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-brand-500 text-sm">
                </div>

                <div>
                    <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1.5">Type <span class="text-red-500">*</span></label>
                    <select name="type" required
                            class="w-full px-3 py-2 rounded-xl border border-gray-200 dark:border-white/10 bg-white dark:bg-[#1a1a27] text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-brand-500 text-sm">
                        @foreach(['consu'=>'Consumable','service'=>'Service','storable'=>'Storable Product','combo'=>'Combo'] as $val => $label)
                            <option value="{{ $val }}" {{ old('type', $product->type) === $val ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1.5">Internal Reference</label>
                        <input type="text" name="reference" value="{{ old('reference', $product->reference) }}"
                               class="w-full px-3 py-2 rounded-xl border border-gray-200 dark:border-white/10 bg-white dark:bg-[#1a1a27] text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-brand-500 text-sm">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1.5">Barcode</label>
                        <input type="text" name="barcode" value="{{ old('barcode', $product->barcode) }}"
                               class="w-full px-3 py-2 rounded-xl border border-gray-200 dark:border-white/10 bg-white dark:bg-[#1a1a27] text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-brand-500 text-sm">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1.5">Sale Price</label>
                        <input type="number" step="0.01" min="0" name="price" value="{{ old('price', $product->price) }}"
                               class="w-full px-3 py-2 rounded-xl border border-gray-200 dark:border-white/10 bg-white dark:bg-[#1a1a27] text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-brand-500 text-sm">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1.5">Cost</label>
                        <input type="number" step="0.01" min="0" name="cost" value="{{ old('cost', $product->cost) }}"
                               class="w-full px-3 py-2 rounded-xl border border-gray-200 dark:border-white/10 bg-white dark:bg-[#1a1a27] text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-brand-500 text-sm">
                    </div>
                </div>

                @if($categories->count())
                    <div>
                        <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1.5">Category</label>
                        <select name="category_id"
                                class="w-full px-3 py-2 rounded-xl border border-gray-200 dark:border-white/10 bg-white dark:bg-[#1a1a27] text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-brand-500 text-sm">
                            <option value="">— Select Category —</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}" {{ old('category_id', $product->category_id) == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                            @endforeach
                        </select>
                    </div>
                @endif
            </div>

            <div class="bg-white dark:bg-[#111118] rounded-2xl shadow-sm border border-gray-100 dark:border-white/5 p-5 space-y-4">
                <h2 class="text-sm font-semibold text-gray-900 dark:text-white">Sales & Purchase</h2>

                <div class="grid grid-cols-2 gap-4">
                    @foreach([
                        'sales_ok' => 'Can be Sold',
                        'purchase_ok' => 'Can be Purchased',
                        'enable_sales' => 'Enable Sales',
                        'enable_purchase' => 'Enable Purchase',
                    ] as $field => $label)
                        <div class="flex items-center gap-2">
                            <input type="checkbox" id="{{ $field }}" name="{{ $field }}" value="1"
                                   {{ old($field, $product->$field) ? 'checked' : '' }}
                                   class="w-4 h-4 rounded border-gray-300 text-brand-600 focus:ring-brand-500">
                            <label for="{{ $field }}" class="text-sm text-gray-700 dark:text-gray-300">{{ $label }}</label>
                        </div>
                    @endforeach
                </div>

                <div>
                    <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1.5">Sales Description</label>
                    <textarea name="description_sale" rows="2"
                              class="w-full px-3 py-2 rounded-xl border border-gray-200 dark:border-white/10 bg-white dark:bg-[#1a1a27] text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-brand-500 text-sm">{{ old('description_sale', $product->description_sale) }}</textarea>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1.5">Purchase Description</label>
                    <textarea name="description_purchase" rows="2"
                              class="w-full px-3 py-2 rounded-xl border border-gray-200 dark:border-white/10 bg-white dark:bg-[#1a1a27] text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-brand-500 text-sm">{{ old('description_purchase', $product->description_purchase ?? '') }}</textarea>
                </div>
            </div>

            <div class="bg-white dark:bg-[#111118] rounded-2xl shadow-sm border border-gray-100 dark:border-white/5 p-5 space-y-4">
                <h2 class="text-sm font-semibold text-gray-900 dark:text-white">Internal Notes</h2>
                <textarea name="description" rows="3"
                          class="w-full px-3 py-2 rounded-xl border border-gray-200 dark:border-white/10 bg-white dark:bg-[#1a1a27] text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-brand-500 text-sm">{{ old('description', $product->description) }}</textarea>
            </div>

            <div class="flex gap-3">
                <button type="submit" class="px-4 py-2 bg-brand-600 hover:bg-brand-700 text-white rounded-xl text-sm font-medium transition-colors">Save Changes</button>
                <a href="{{ route('products.show', $product->id) }}" class="px-4 py-2 bg-gray-100 dark:bg-white/5 hover:bg-gray-200 dark:hover:bg-white/10 text-gray-700 dark:text-gray-300 rounded-xl text-sm font-medium transition-colors">Cancel</a>
            </div>
        </form>
    </div>
@endsection

@extends('app.layouts.app')
@section('title', 'New Sales Order')
@section('breadcrumb')
<nav class="text-sm text-gray-500 dark:text-gray-400">
    <a href="/app/dashboard" class="hover:text-brand-600">Home</a>
    <span class="mx-2">/</span>
    <a href="{{ route('sales.index') }}" class="hover:text-brand-600">Sales Orders</a>
    <span class="mx-2">/</span>
    <span class="text-gray-900 dark:text-white">New Order</span>
</nav>
@endsection
@section('content')

@if(session('error'))
<div class="mb-4 px-4 py-3 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-xl text-red-700 dark:text-red-400 text-sm">{{ session('error') }}</div>
@endif

<div class="flex items-center justify-between mb-6">
    <div>
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">New Sales Order</h1>
        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Create a new customer sales order</p>
    </div>
</div>

<div class="max-w-2xl">
    <form method="POST" action="{{ route('sales.store') }}">
        @csrf
        <div class="bg-white dark:bg-[#111118] rounded-2xl shadow-sm border border-gray-100 dark:border-white/5 p-6 space-y-5">

            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Customer</label>
                <select name="partner_id" class="w-full px-3 py-2 rounded-xl border border-gray-200 dark:border-white/10 bg-white dark:bg-[#1a1a27] text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-brand-500">
                    <option value="">— Select customer —</option>
                    @foreach($partners as $partner)
                    <option value="{{ $partner->id }}" {{ old('partner_id') == $partner->id ? 'selected' : '' }}>{{ $partner->name }}</option>
                    @endforeach
                </select>
                @error('partner_id') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Customer Reference</label>
                <input type="text" name="client_order_ref" value="{{ old('client_order_ref') }}" placeholder="e.g. PO-12345"
                       class="w-full px-3 py-2 rounded-xl border border-gray-200 dark:border-white/10 bg-white dark:bg-[#1a1a27] text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-brand-500">
                <p class="mt-1 text-xs text-gray-400">Customer's own purchase order reference number</p>
                @error('client_order_ref') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Validity Date</label>
                <input type="date" name="validity_date" value="{{ old('validity_date') }}"
                       class="w-full px-3 py-2 rounded-xl border border-gray-200 dark:border-white/10 bg-white dark:bg-[#1a1a27] text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-brand-500">
                <p class="mt-1 text-xs text-gray-400">Date until which the quotation is valid</p>
                @error('validity_date') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Notes</label>
                <textarea name="note" rows="4" placeholder="Internal notes or terms…"
                          class="w-full px-3 py-2 rounded-xl border border-gray-200 dark:border-white/10 bg-white dark:bg-[#1a1a27] text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-brand-500">{{ old('note') }}</textarea>
                @error('note') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
            </div>

        </div>

        <div class="flex items-center gap-3 mt-5">
            <button type="submit" class="px-4 py-2 bg-brand-600 hover:bg-brand-700 text-white rounded-xl text-sm font-medium transition-colors">
                Create Sales Order
            </button>
            <a href="{{ route('sales.index') }}" class="px-4 py-2 bg-gray-100 dark:bg-white/5 hover:bg-gray-200 dark:hover:bg-white/10 text-gray-700 dark:text-gray-300 rounded-xl text-sm font-medium transition-colors">
                Cancel
            </a>
        </div>
    </form>
</div>
@endsection

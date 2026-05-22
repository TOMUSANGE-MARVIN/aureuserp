@extends('app.layouts.app')
@section('title', 'Edit ' . $order->name)
@section('breadcrumb')
<nav class="text-sm text-gray-500 dark:text-gray-400">
    <a href="/app/dashboard" class="hover:text-brand-600">Home</a>
    <span class="mx-2">/</span>
    <a href="{{ route('purchases.index') }}" class="hover:text-brand-600">Purchase Orders</a>
    <span class="mx-2">/</span>
    <a href="{{ route('purchases.show', $order->id) }}" class="hover:text-brand-600">{{ $order->name }}</a>
    <span class="mx-2">/</span>
    <span class="text-gray-900 dark:text-white">Edit</span>
</nav>
@endsection
@section('content')

@if(session('error'))
<div class="mb-4 px-4 py-3 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-xl text-red-700 dark:text-red-400 text-sm">{{ session('error') }}</div>
@endif

<div class="flex items-center justify-between mb-6">
    <div>
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Edit {{ $order->name }}</h1>
        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Update purchase order details</p>
    </div>
</div>

<div class="max-w-2xl">
    <form method="POST" action="{{ route('purchases.update', $order->id) }}">
        @csrf
        @method('PUT')
        <div class="bg-white dark:bg-[#111118] rounded-2xl shadow-sm border border-gray-100 dark:border-white/5 p-6 space-y-5">

            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Status</label>
                <select name="state" class="w-full px-3 py-2 rounded-xl border border-gray-200 dark:border-white/10 bg-white dark:bg-[#1a1a27] text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-brand-500">
                    <option value="draft"    {{ old('state', $order->state) === 'draft'    ? 'selected' : '' }}>Draft</option>
                    <option value="purchase" {{ old('state', $order->state) === 'purchase' ? 'selected' : '' }}>Confirmed</option>
                    <option value="cancel"   {{ old('state', $order->state) === 'cancel'   ? 'selected' : '' }}>Cancelled</option>
                </select>
                @error('state') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Vendor</label>
                <select name="partner_id" class="w-full px-3 py-2 rounded-xl border border-gray-200 dark:border-white/10 bg-white dark:bg-[#1a1a27] text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-brand-500">
                    <option value="">— Select vendor —</option>
                    @foreach($partners as $partner)
                    <option value="{{ $partner->id }}" {{ old('partner_id', $order->partner_id) == $partner->id ? 'selected' : '' }}>{{ $partner->name }}</option>
                    @endforeach
                </select>
                @error('partner_id') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Priority</label>
                <select name="priority" class="w-full px-3 py-2 rounded-xl border border-gray-200 dark:border-white/10 bg-white dark:bg-[#1a1a27] text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-brand-500">
                    <option value="normal" {{ old('priority', $order->priority ?? 'normal') === 'normal' ? 'selected' : '' }}>Normal</option>
                    <option value="urgent" {{ old('priority', $order->priority ?? 'normal') === 'urgent' ? 'selected' : '' }}>Urgent</option>
                </select>
                @error('priority') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Notes</label>
                <textarea name="notes" rows="4"
                          class="w-full px-3 py-2 rounded-xl border border-gray-200 dark:border-white/10 bg-white dark:bg-[#1a1a27] text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-brand-500">{{ old('notes', $order->notes ?? '') }}</textarea>
                @error('notes') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
            </div>

        </div>

        <div class="flex items-center gap-3 mt-5">
            <button type="submit" class="px-4 py-2 bg-brand-600 hover:bg-brand-700 text-white rounded-xl text-sm font-medium transition-colors">
                Save Changes
            </button>
            <a href="{{ route('purchases.show', $order->id) }}" class="px-4 py-2 bg-gray-100 dark:bg-white/5 hover:bg-gray-200 dark:hover:bg-white/10 text-gray-700 dark:text-gray-300 rounded-xl text-sm font-medium transition-colors">
                Cancel
            </a>
        </div>
    </form>
</div>
@endsection

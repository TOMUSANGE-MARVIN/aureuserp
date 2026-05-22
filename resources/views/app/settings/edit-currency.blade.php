@extends('app.layouts.app')
@section('title', 'Edit Currency')
@section('breadcrumb', 'Settings')

@section('content')
    <div class="max-w-lg">
        <div class="flex items-center justify-between mb-5">
            <div>
                <h1 class="text-xl font-bold text-gray-900 dark:text-white">{{ $currency->name }}</h1>
                <p class="text-sm text-gray-500 mt-1">{{ $currency->full_name ?? $currency->name }} &bull; {{ $currency->symbol }}</p>
            </div>
            <a href="{{ route('settings.currencies') }}" class="px-4 py-2 bg-gray-100 dark:bg-white/5 hover:bg-gray-200 dark:hover:bg-white/10 text-gray-700 dark:text-gray-300 rounded-xl text-sm font-medium transition-colors">Cancel</a>
        </div>

        @if(session('success'))
            <div class="mb-5 px-4 py-3 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-xl text-sm text-green-700 dark:text-green-400">{{ session('success') }}</div>
        @endif

        @if($errors->any())
            <div class="mb-5 px-4 py-3 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-xl">
                <ul class="list-disc list-inside text-sm text-red-700 dark:text-red-400 space-y-1">
                    @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('settings.currencies.update', $currency->id) }}" class="space-y-5">
            @csrf

            <div class="bg-white dark:bg-[#111118] rounded-2xl border border-gray-100 dark:border-white/5 p-5 space-y-5">
                {{-- Read-only info --}}
                <div class="grid grid-cols-3 gap-4">
                    <div>
                        <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Code</label>
                        <p class="text-sm font-semibold text-gray-900 dark:text-white">{{ $currency->name }}</p>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Symbol</label>
                        <p class="text-sm font-semibold text-gray-900 dark:text-white">{{ $currency->symbol }}</p>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Decimal Places</label>
                        <p class="text-sm font-semibold text-gray-900 dark:text-white">{{ $currency->decimal_places }}</p>
                    </div>
                </div>

                {{-- Editable fields --}}
                <div class="border-t border-gray-100 dark:border-white/5 pt-5 space-y-4">
                    <div>
                        <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1.5">Rounding Factor</label>
                        <input type="number" name="rounding" value="{{ old('rounding', $currency->rounding) }}"
                               step="0.001" min="0"
                               class="w-full px-3 py-2 rounded-xl border border-gray-200 dark:border-white/10 bg-white dark:bg-[#1a1a27] text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-brand-500 text-sm">
                        <p class="text-xs text-gray-400 mt-1">Controls how amounts are rounded (e.g., 0.01 for 2 decimal places)</p>
                    </div>

                    <div class="flex items-center gap-3">
                        <input type="hidden" name="active" value="0">
                        <input type="checkbox" id="active" name="active" value="1" {{ old('active', $currency->active) ? 'checked' : '' }}
                               class="w-4 h-4 rounded border-gray-300 text-brand-600 focus:ring-brand-500">
                        <div>
                            <label for="active" class="text-sm font-medium text-gray-700 dark:text-gray-300">Active</label>
                            <p class="text-xs text-gray-400">Inactive currencies are hidden from transaction forms</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="flex gap-3">
                <button type="submit" class="px-4 py-2 bg-brand-600 hover:bg-brand-700 text-white rounded-xl text-sm font-medium transition-colors">Save Changes</button>
                <a href="{{ route('settings.currencies') }}" class="px-4 py-2 bg-gray-100 dark:bg-white/5 hover:bg-gray-200 dark:hover:bg-white/10 text-gray-700 dark:text-gray-300 rounded-xl text-sm font-medium transition-colors">Cancel</a>
            </div>
        </form>
    </div>
@endsection

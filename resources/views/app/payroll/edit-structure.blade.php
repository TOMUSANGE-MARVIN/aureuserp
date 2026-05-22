@extends('app.layouts.app')
@section('title', 'Edit Structure')
@section('breadcrumb', 'Payroll / Structures / ' . $structure->name)

@section('content')
@if(session('success'))
<div class="bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-xl p-4 text-sm text-green-800 dark:text-green-300">{{ session('success') }}</div>
@endif
@if(session('error'))
<div class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-xl p-4 text-sm text-red-800 dark:text-red-300">{{ session('error') }}</div>
@endif

<div class="flex items-center justify-between">
    <h1 class="text-xl font-bold text-gray-900 dark:text-white">{{ $structure->name }}</h1>
    <a href="{{ route('payroll.structures') }}" class="text-sm text-gray-500 dark:text-gray-400 hover:underline">← Structures</a>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    {{-- Structure Details --}}
    <div class="lg:col-span-1">
        <div class="bg-white dark:bg-[#111118] rounded-2xl border border-gray-100 dark:border-white/5 p-5">
            <h2 class="text-sm font-semibold text-gray-900 dark:text-white mb-4">Structure Details</h2>
            <form method="POST" action="{{ route('payroll.structures.update', $structure->id) }}" class="space-y-4">
                @csrf @method('PUT')
                <div>
                    <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Name *</label>
                    <input type="text" name="name" value="{{ $structure->name }}" required
                           class="w-full px-3 py-2 text-sm border border-gray-200 dark:border-white/10 rounded-xl bg-white dark:bg-[#111118] text-gray-900 dark:text-white">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Description</label>
                    <textarea name="description" rows="3"
                              class="w-full px-3 py-2 text-sm border border-gray-200 dark:border-white/10 rounded-xl bg-white dark:bg-[#111118] text-gray-900 dark:text-white resize-none">{{ $structure->description }}</textarea>
                </div>
                <div class="flex items-center gap-2">
                    <input type="checkbox" name="is_active" id="is_active" value="1" {{ $structure->is_active ? 'checked' : '' }}
                           class="rounded border-gray-300 text-brand-600">
                    <label for="is_active" class="text-sm text-gray-700 dark:text-gray-300">Active</label>
                </div>
                <button type="submit" class="w-full px-4 py-2 text-sm bg-brand-600 hover:bg-brand-700 text-white rounded-xl transition font-medium">Save</button>
            </form>
        </div>
    </div>

    {{-- Rules --}}
    <div class="lg:col-span-2 space-y-5">
        {{-- Add Rule --}}
        <div class="bg-white dark:bg-[#111118] rounded-2xl border border-gray-100 dark:border-white/5 p-5">
            <h2 class="text-sm font-semibold text-gray-900 dark:text-white mb-4">Add Rule</h2>
            <form method="POST" action="{{ route('payroll.structures.rules.store', $structure->id) }}" class="space-y-3">
                @csrf
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Name *</label>
                        <input type="text" name="name" required placeholder="e.g. Housing Allowance"
                               class="w-full px-3 py-2 text-sm border border-gray-200 dark:border-white/10 rounded-xl bg-white dark:bg-[#111118] text-gray-900 dark:text-white">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Code *</label>
                        <input type="text" name="code" required placeholder="e.g. HOUSE" maxlength="50"
                               class="w-full px-3 py-2 text-sm border border-gray-200 dark:border-white/10 rounded-xl bg-white dark:bg-[#111118] text-gray-900 dark:text-white uppercase">
                    </div>
                </div>
                <div class="grid grid-cols-3 gap-3">
                    <div>
                        <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Type</label>
                        <select name="type" class="w-full px-3 py-2 text-sm border border-gray-200 dark:border-white/10 rounded-xl bg-white dark:bg-[#111118] text-gray-900 dark:text-white">
                            <option value="earning">Earning</option>
                            <option value="deduction">Deduction</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Amount Type</label>
                        <select name="amount_type" class="w-full px-3 py-2 text-sm border border-gray-200 dark:border-white/10 rounded-xl bg-white dark:bg-[#111118] text-gray-900 dark:text-white">
                            <option value="fixed">Fixed</option>
                            <option value="percentage">% of Basic</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Amount *</label>
                        <input type="number" name="amount" min="0" step="0.01" required placeholder="0.00"
                               class="w-full px-3 py-2 text-sm border border-gray-200 dark:border-white/10 rounded-xl bg-white dark:bg-[#111118] text-gray-900 dark:text-white">
                    </div>
                </div>
                <div class="flex items-end gap-3">
                    <div class="w-32">
                        <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Sequence</label>
                        <input type="number" name="sequence" min="1" value="10" placeholder="10"
                               class="w-full px-3 py-2 text-sm border border-gray-200 dark:border-white/10 rounded-xl bg-white dark:bg-[#111118] text-gray-900 dark:text-white">
                    </div>
                    <button type="submit" class="px-4 py-2 text-sm bg-brand-600 hover:bg-brand-700 text-white rounded-xl transition font-medium">Add Rule</button>
                </div>
            </form>
        </div>

        {{-- Rules List --}}
        <div class="bg-white dark:bg-[#111118] rounded-2xl border border-gray-100 dark:border-white/5 overflow-hidden">
            <div class="px-5 py-3 border-b border-gray-100 dark:border-white/5">
                <h2 class="text-sm font-semibold text-gray-900 dark:text-white">Rules ({{ $rules->count() }})</h2>
            </div>
            @if($rules->isEmpty())
            <div class="px-5 py-8 text-center text-sm text-gray-400 dark:text-gray-500">No rules yet. Add rules above to define earnings and deductions.</div>
            @else
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-gray-100 dark:border-white/5">
                        <th class="text-left px-4 py-2 text-xs font-semibold text-gray-500 dark:text-gray-400">#</th>
                        <th class="text-left px-4 py-2 text-xs font-semibold text-gray-500 dark:text-gray-400">Name</th>
                        <th class="text-left px-4 py-2 text-xs font-semibold text-gray-500 dark:text-gray-400">Code</th>
                        <th class="text-left px-4 py-2 text-xs font-semibold text-gray-500 dark:text-gray-400">Type</th>
                        <th class="text-right px-4 py-2 text-xs font-semibold text-gray-500 dark:text-gray-400">Amount</th>
                        <th class="px-4 py-2"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50 dark:divide-white/[0.03]">
                    @foreach($rules as $rule)
                    <tr class="hover:bg-gray-50 dark:hover:bg-white/[0.02]">
                        <td class="px-4 py-2 text-gray-400 dark:text-gray-500">{{ $rule->sequence }}</td>
                        <td class="px-4 py-2 font-medium text-gray-900 dark:text-white">{{ $rule->name }}</td>
                        <td class="px-4 py-2 text-gray-500 dark:text-gray-400 font-mono text-xs">{{ $rule->code }}</td>
                        <td class="px-4 py-2">
                            @if($rule->type === 'earning')
                                <span class="px-2 py-0.5 text-xs rounded-full bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400">Earning</span>
                            @else
                                <span class="px-2 py-0.5 text-xs rounded-full bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-400">Deduction</span>
                            @endif
                        </td>
                        <td class="px-4 py-2 text-right font-medium text-gray-900 dark:text-white">
                            {{ $rule->amount_type === 'percentage' ? $rule->amount.'%' : number_format($rule->amount, 2) }}
                            <span class="text-xs text-gray-400">{{ $rule->amount_type === 'percentage' ? 'of basic' : 'fixed' }}</span>
                        </td>
                        <td class="px-4 py-2 text-right">
                            <form method="POST" action="{{ route('payroll.structures.rules.delete', [$structure->id, $rule->id]) }}" class="inline" onsubmit="return confirm('Delete this rule?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="text-red-500 dark:text-red-400 hover:underline text-xs">Delete</button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            @endif
        </div>
    </div>
</div>
@endsection

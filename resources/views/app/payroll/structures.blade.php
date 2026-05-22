@extends('app.layouts.app')
@section('title', 'Salary Structures')
@section('breadcrumb', 'Payroll / Salary Structures')

@section('content')
@if(session('success'))
<div class="bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-xl p-4 text-sm text-green-800 dark:text-green-300">{{ session('success') }}</div>
@endif
@if(session('error'))
<div class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-xl p-4 text-sm text-red-800 dark:text-red-300">{{ session('error') }}</div>
@endif

<div class="flex items-center justify-between">
    <div>
        <h1 class="text-xl font-bold text-gray-900 dark:text-white">Salary Structures</h1>
        <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">Define pay structures with earnings and deduction rules</p>
    </div>
    <a href="{{ route('payroll.index') }}" class="text-sm text-gray-500 dark:text-gray-400 hover:underline">← Payroll</a>
</div>

{{-- Create Structure Form --}}
<div class="bg-white dark:bg-[#111118] rounded-2xl border border-gray-100 dark:border-white/5 p-5">
    <h2 class="text-sm font-semibold text-gray-900 dark:text-white mb-4">New Structure</h2>
    <form method="POST" action="{{ route('payroll.structures.store') }}" class="flex flex-wrap gap-3 items-end">
        @csrf
        <div class="flex-1 min-w-48">
            <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Name *</label>
            <input type="text" name="name" required placeholder="e.g. Standard Monthly" value="{{ old('name') }}"
                   class="w-full px-3 py-2 text-sm border border-gray-200 dark:border-white/10 rounded-xl bg-white dark:bg-[#111118] text-gray-900 dark:text-white">
        </div>
        <div class="flex-1 min-w-48">
            <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Description</label>
            <input type="text" name="description" placeholder="Optional description" value="{{ old('description') }}"
                   class="w-full px-3 py-2 text-sm border border-gray-200 dark:border-white/10 rounded-xl bg-white dark:bg-[#111118] text-gray-900 dark:text-white">
        </div>
        <button type="submit" class="px-4 py-2 text-sm bg-brand-600 hover:bg-brand-700 text-white rounded-xl transition font-medium">Create</button>
    </form>
</div>

{{-- Structures List --}}
<div class="bg-white dark:bg-[#111118] rounded-2xl border border-gray-100 dark:border-white/5 overflow-hidden">
    <table class="w-full text-sm">
        <thead>
            <tr class="border-b border-gray-100 dark:border-white/5">
                <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide">Name</th>
                <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide">Description</th>
                <th class="text-center px-4 py-3 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide">Rules</th>
                <th class="text-center px-4 py-3 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide">Payslips</th>
                <th class="text-center px-4 py-3 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide">Active</th>
                <th class="px-4 py-3"></th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-50 dark:divide-white/[0.03]">
            @forelse($structures as $str)
            <tr class="hover:bg-gray-50 dark:hover:bg-white/[0.02] transition">
                <td class="px-4 py-3 font-medium text-gray-900 dark:text-white">{{ $str->name }}</td>
                <td class="px-4 py-3 text-gray-500 dark:text-gray-400">{{ $str->description ?: '—' }}</td>
                <td class="px-4 py-3 text-center text-gray-600 dark:text-gray-300">{{ $str->rules_count }}</td>
                <td class="px-4 py-3 text-center text-gray-600 dark:text-gray-300">{{ $str->payslip_count }}</td>
                <td class="px-4 py-3 text-center">
                    @if($str->is_active)
                        <span class="px-2 py-0.5 text-xs rounded-full bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400">Active</span>
                    @else
                        <span class="px-2 py-0.5 text-xs rounded-full bg-gray-100 dark:bg-gray-800 text-gray-500 dark:text-gray-400">Inactive</span>
                    @endif
                </td>
                <td class="px-4 py-3 text-right">
                    <a href="{{ route('payroll.structures.edit', $str->id) }}" class="text-brand-600 dark:text-brand-400 hover:underline text-xs font-medium mr-3">Edit Rules</a>
                    @if($str->payslip_count == 0)
                    <form method="POST" action="{{ route('payroll.structures.delete', $str->id) }}" class="inline" onsubmit="return confirm('Delete this structure?')">
                        @csrf @method('DELETE')
                        <button type="submit" class="text-red-500 dark:text-red-400 hover:underline text-xs">Delete</button>
                    </form>
                    @endif
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="6" class="px-4 py-12 text-center text-gray-400 dark:text-gray-500">No salary structures yet. Create one above.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection

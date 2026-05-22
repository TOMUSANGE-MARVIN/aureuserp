@extends('app.layouts.app')
@section('title', 'New Payslip')
@section('breadcrumb', 'Payroll / New Payslip')

@section('content')
<div class="flex items-center justify-between">
    <h1 class="text-xl font-bold text-gray-900 dark:text-white">New Payslip</h1>
    <a href="{{ route('payroll.index') }}" class="text-sm text-gray-500 dark:text-gray-400 hover:underline">← Back to Payroll</a>
</div>

@if($errors->any())
<div class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-xl p-4 text-sm text-red-700 dark:text-red-300">
    <ul class="list-disc pl-4 space-y-1">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
</div>
@endif

<div class="bg-white dark:bg-[#111118] rounded-2xl border border-gray-100 dark:border-white/5 p-6 max-w-2xl">
    <form method="POST" action="{{ route('payroll.store') }}" class="space-y-5">
        @csrf
        <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Employee *</label>
            <select name="employee_id" required class="w-full px-3 py-2 text-sm border border-gray-200 dark:border-white/10 rounded-xl bg-white dark:bg-[#111118] text-gray-900 dark:text-white">
                <option value="">Select employee…</option>
                @foreach($employees as $emp)
                    <option value="{{ $emp->id }}" {{ old('employee_id') == $emp->id ? 'selected' : '' }}>
                        {{ $emp->name }}{{ $emp->job_title ? ' — '.$emp->job_title : '' }}
                    </option>
                @endforeach
            </select>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Salary Structure *</label>
            <select name="structure_id" required class="w-full px-3 py-2 text-sm border border-gray-200 dark:border-white/10 rounded-xl bg-white dark:bg-[#111118] text-gray-900 dark:text-white">
                <option value="">Select structure…</option>
                @foreach($structures as $str)
                    <option value="{{ $str->id }}" {{ old('structure_id') == $str->id ? 'selected' : '' }}>{{ $str->name }}</option>
                @endforeach
            </select>
            @if($structures->isEmpty())
                <p class="text-xs text-yellow-600 dark:text-yellow-400 mt-1">No salary structures yet. <a href="{{ route('payroll.structures') }}" class="underline">Create one first.</a></p>
            @endif
        </div>

        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Period Start *</label>
                <input type="date" name="period_start" value="{{ old('period_start', now()->startOfMonth()->format('Y-m-d')) }}" required
                       class="w-full px-3 py-2 text-sm border border-gray-200 dark:border-white/10 rounded-xl bg-white dark:bg-[#111118] text-gray-900 dark:text-white">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Period End *</label>
                <input type="date" name="period_end" value="{{ old('period_end', now()->endOfMonth()->format('Y-m-d')) }}" required
                       class="w-full px-3 py-2 text-sm border border-gray-200 dark:border-white/10 rounded-xl bg-white dark:bg-[#111118] text-gray-900 dark:text-white">
            </div>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Basic Salary *</label>
            <input type="number" name="basic_salary" value="{{ old('basic_salary', '0.00') }}" min="0" step="0.01" required
                   class="w-full px-3 py-2 text-sm border border-gray-200 dark:border-white/10 rounded-xl bg-white dark:bg-[#111118] text-gray-900 dark:text-white">
            <p class="text-xs text-gray-400 mt-1">Allowances and deductions will be auto-calculated from the selected structure's rules.</p>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Internal Note</label>
            <textarea name="note" rows="2" placeholder="Optional note…"
                      class="w-full px-3 py-2 text-sm border border-gray-200 dark:border-white/10 rounded-xl bg-white dark:bg-[#111118] text-gray-900 dark:text-white resize-none">{{ old('note') }}</textarea>
        </div>

        <div class="flex gap-3 pt-2">
            <button type="submit" class="px-5 py-2 text-sm bg-brand-600 hover:bg-brand-700 text-white rounded-xl transition font-medium">Create Payslip</button>
            <a href="{{ route('payroll.index') }}" class="px-5 py-2 text-sm border border-gray-200 dark:border-white/10 text-gray-600 dark:text-gray-300 rounded-xl hover:bg-gray-50 dark:hover:bg-white/5 transition">Cancel</a>
        </div>
    </form>
</div>
@endsection

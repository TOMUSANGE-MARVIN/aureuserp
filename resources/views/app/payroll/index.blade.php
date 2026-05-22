@extends('app.layouts.app')
@section('title', 'Payroll')
@section('breadcrumb', 'Payroll')

@section('content')
@if(session('success'))
<div class="bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-xl p-4 flex items-center gap-3 text-sm text-green-800 dark:text-green-300">
    <svg class="w-4 h-4 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
    {{ session('success') }}
</div>
@endif
@if(session('error'))
<div class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-xl p-4 text-sm text-red-800 dark:text-red-300">{{ session('error') }}</div>
@endif

{{-- Page Header --}}
<div class="flex items-center justify-between">
    <div>
        <h1 class="text-xl font-bold text-gray-900 dark:text-white">Payroll</h1>
        <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">Manage employee payslips and salary processing</p>
    </div>
    <div class="flex items-center gap-2">
        <a href="{{ route('payroll.structures') }}"
           class="px-3 py-2 text-sm border border-gray-200 dark:border-white/10 rounded-xl text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-white/5 transition">
            Salary Structures
        </a>
        <button onclick="document.getElementById('run-payroll-modal').classList.remove('hidden')"
                class="px-4 py-2 text-sm bg-brand-600 hover:bg-brand-700 text-white rounded-xl transition font-medium">
            Run Payroll
        </button>
        <a href="{{ route('payroll.create') }}"
           class="px-4 py-2 text-sm bg-gray-900 dark:bg-white/10 hover:bg-gray-800 dark:hover:bg-white/20 text-white rounded-xl transition font-medium">
            + New Payslip
        </a>
    </div>
</div>

{{-- Stats --}}
<div class="grid grid-cols-2 md:grid-cols-4 gap-4">
    <div class="bg-white dark:bg-[#111118] rounded-2xl border border-gray-100 dark:border-white/5 p-4">
        <p class="text-xs text-gray-500 dark:text-gray-400 font-medium uppercase tracking-wide">Payslips</p>
        <p class="text-2xl font-bold text-gray-900 dark:text-white mt-1">{{ $stats->total ?? 0 }}</p>
    </div>
    <div class="bg-white dark:bg-[#111118] rounded-2xl border border-gray-100 dark:border-white/5 p-4">
        <p class="text-xs text-gray-500 dark:text-gray-400 font-medium uppercase tracking-wide">Gross Payroll</p>
        <p class="text-2xl font-bold text-blue-600 dark:text-blue-400 mt-1">{{ number_format($stats->total_gross ?? 0, 0) }}</p>
    </div>
    <div class="bg-white dark:bg-[#111118] rounded-2xl border border-gray-100 dark:border-white/5 p-4">
        <p class="text-xs text-gray-500 dark:text-gray-400 font-medium uppercase tracking-wide">Total Deductions</p>
        <p class="text-2xl font-bold text-red-500 dark:text-red-400 mt-1">{{ number_format($stats->total_deductions ?? 0, 0) }}</p>
    </div>
    <div class="bg-white dark:bg-[#111118] rounded-2xl border border-gray-100 dark:border-white/5 p-4">
        <p class="text-xs text-gray-500 dark:text-gray-400 font-medium uppercase tracking-wide">Net Payroll</p>
        <p class="text-2xl font-bold text-green-600 dark:text-green-400 mt-1">{{ number_format($stats->total_net ?? 0, 0) }}</p>
    </div>
</div>

{{-- Filters --}}
<form method="GET" class="flex flex-wrap gap-3 items-end">
    <div>
        <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1">Month</label>
        <input type="month" name="month" value="{{ $month }}"
               class="px-3 py-2 text-sm border border-gray-200 dark:border-white/10 rounded-xl bg-white dark:bg-[#111118] text-gray-900 dark:text-white">
    </div>
    <div>
        <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1">Employee</label>
        <select name="employee_id" class="px-3 py-2 text-sm border border-gray-200 dark:border-white/10 rounded-xl bg-white dark:bg-[#111118] text-gray-900 dark:text-white">
            <option value="">All Employees</option>
            @foreach($employees as $emp)
                <option value="{{ $emp->id }}" {{ $empId == $emp->id ? 'selected' : '' }}>{{ $emp->name }}</option>
            @endforeach
        </select>
    </div>
    <div>
        <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1">Status</label>
        <select name="status" class="px-3 py-2 text-sm border border-gray-200 dark:border-white/10 rounded-xl bg-white dark:bg-[#111118] text-gray-900 dark:text-white">
            <option value="">All Statuses</option>
            <option value="draft" {{ $status === 'draft' ? 'selected' : '' }}>Draft</option>
            <option value="confirmed" {{ $status === 'confirmed' ? 'selected' : '' }}>Confirmed</option>
            <option value="paid" {{ $status === 'paid' ? 'selected' : '' }}>Paid</option>
        </select>
    </div>
    <button type="submit" class="px-4 py-2 text-sm bg-brand-600 hover:bg-brand-700 text-white rounded-xl transition">Filter</button>
</form>

{{-- Payslips Table --}}
<div class="bg-white dark:bg-[#111118] rounded-2xl border border-gray-100 dark:border-white/5 overflow-hidden">
    <table class="w-full text-sm">
        <thead>
            <tr class="border-b border-gray-100 dark:border-white/5">
                <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide">Employee</th>
                <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide">Period</th>
                <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide">Structure</th>
                <th class="text-right px-4 py-3 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide">Gross</th>
                <th class="text-right px-4 py-3 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide">Deductions</th>
                <th class="text-right px-4 py-3 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide">Net</th>
                <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide">Status</th>
                <th class="px-4 py-3"></th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-50 dark:divide-white/[0.03]">
            @forelse($payslips as $slip)
            <tr class="hover:bg-gray-50 dark:hover:bg-white/[0.02] transition">
                <td class="px-4 py-3">
                    <p class="font-medium text-gray-900 dark:text-white">{{ $slip->employee_name }}</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400">{{ $slip->job_title }}</p>
                </td>
                <td class="px-4 py-3 text-gray-600 dark:text-gray-300">
                    {{ \Carbon\Carbon::parse($slip->period_start)->format('M d') }} – {{ \Carbon\Carbon::parse($slip->period_end)->format('M d, Y') }}
                </td>
                <td class="px-4 py-3 text-gray-600 dark:text-gray-300">{{ $slip->structure_name ?? '—' }}</td>
                <td class="px-4 py-3 text-right font-medium text-gray-900 dark:text-white">{{ number_format($slip->gross_salary, 2) }}</td>
                <td class="px-4 py-3 text-right text-red-500 dark:text-red-400">{{ number_format($slip->total_deductions, 2) }}</td>
                <td class="px-4 py-3 text-right font-bold text-green-600 dark:text-green-400">{{ number_format($slip->net_salary, 2) }}</td>
                <td class="px-4 py-3">
                    @if($slip->status === 'paid')
                        <span class="px-2 py-0.5 text-xs rounded-full bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400">Paid</span>
                    @elseif($slip->status === 'confirmed')
                        <span class="px-2 py-0.5 text-xs rounded-full bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-400">Confirmed</span>
                    @else
                        <span class="px-2 py-0.5 text-xs rounded-full bg-yellow-100 dark:bg-yellow-900/30 text-yellow-700 dark:text-yellow-400">Draft</span>
                    @endif
                </td>
                <td class="px-4 py-3 text-right">
                    <a href="{{ route('payroll.show', $slip->id) }}" class="text-brand-600 dark:text-brand-400 hover:underline text-xs font-medium">View</a>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="8" class="px-4 py-12 text-center text-gray-400 dark:text-gray-500">
                    No payslips found for this period.
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
    @if($payslips->hasPages())
    <div class="px-4 py-3 border-t border-gray-100 dark:border-white/5">
        {{ $payslips->links() }}
    </div>
    @endif
</div>

{{-- Run Payroll Modal --}}
<div id="run-payroll-modal" class="hidden fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4">
    <div class="bg-white dark:bg-[#111118] rounded-2xl shadow-xl w-full max-w-md p-6">
        <h2 class="text-lg font-bold text-gray-900 dark:text-white mb-4">Run Payroll</h2>
        <form method="POST" action="{{ route('payroll.run') }}" class="space-y-4">
            @csrf
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Salary Structure</label>
                <select name="structure_id" required class="w-full px-3 py-2 text-sm border border-gray-200 dark:border-white/10 rounded-xl bg-white dark:bg-[#111118] text-gray-900 dark:text-white">
                    <option value="">Select a structure…</option>
                    @foreach(\Illuminate\Support\Facades\DB::table('payroll_salary_structures')->where('is_active',1)->orderBy('name')->get() as $str)
                        <option value="{{ $str->id }}">{{ $str->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Period Start</label>
                    <input type="date" name="period_start" required class="w-full px-3 py-2 text-sm border border-gray-200 dark:border-white/10 rounded-xl bg-white dark:bg-[#111118] text-gray-900 dark:text-white">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Period End</label>
                    <input type="date" name="period_end" required class="w-full px-3 py-2 text-sm border border-gray-200 dark:border-white/10 rounded-xl bg-white dark:bg-[#111118] text-gray-900 dark:text-white">
                </div>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Default Basic Salary</label>
                <input type="number" name="basic_salary" min="0" step="0.01" required placeholder="0.00"
                       class="w-full px-3 py-2 text-sm border border-gray-200 dark:border-white/10 rounded-xl bg-white dark:bg-[#111118] text-gray-900 dark:text-white">
                <p class="text-xs text-gray-400 mt-1">Applied to all employees. Overrides can be set per payslip.</p>
            </div>
            <div class="flex gap-3 pt-2">
                <button type="submit" class="flex-1 px-4 py-2 text-sm bg-brand-600 hover:bg-brand-700 text-white rounded-xl transition font-medium">Generate Payslips</button>
                <button type="button" onclick="document.getElementById('run-payroll-modal').classList.add('hidden')"
                        class="flex-1 px-4 py-2 text-sm border border-gray-200 dark:border-white/10 text-gray-600 dark:text-gray-300 rounded-xl hover:bg-gray-50 dark:hover:bg-white/5 transition">Cancel</button>
            </div>
        </form>
    </div>
</div>
@endsection

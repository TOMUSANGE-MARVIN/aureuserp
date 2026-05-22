@extends('app.layouts.app')
@section('title', 'Payslip')
@section('breadcrumb', 'Payroll / Payslip #' . $payslip->id)

@section('content')
@if(session('success'))
<div class="bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-xl p-4 text-sm text-green-800 dark:text-green-300">{{ session('success') }}</div>
@endif
@if(session('error'))
<div class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-xl p-4 text-sm text-red-800 dark:text-red-300">{{ session('error') }}</div>
@endif

<div class="flex items-center justify-between">
    <div class="flex items-center gap-3">
        <a href="{{ route('payroll.index') }}" class="text-sm text-gray-500 dark:text-gray-400 hover:underline">← Payroll</a>
        <span class="text-gray-300 dark:text-white/20">/</span>
        <h1 class="text-xl font-bold text-gray-900 dark:text-white">Payslip #{{ $payslip->id }}</h1>
        @if($payslip->status === 'paid')
            <span class="px-2.5 py-0.5 text-xs rounded-full bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400 font-medium">Paid</span>
        @elseif($payslip->status === 'confirmed')
            <span class="px-2.5 py-0.5 text-xs rounded-full bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-400 font-medium">Confirmed</span>
        @else
            <span class="px-2.5 py-0.5 text-xs rounded-full bg-yellow-100 dark:bg-yellow-900/30 text-yellow-700 dark:text-yellow-400 font-medium">Draft</span>
        @endif
    </div>
    <div class="flex items-center gap-2 no-print">
        @if($payslip->status === 'draft')
        <form method="POST" action="{{ route('payroll.confirm', $payslip->id) }}" class="inline">
            @csrf
            <button type="submit" class="px-4 py-2 text-sm bg-blue-600 hover:bg-blue-700 text-white rounded-xl transition font-medium">Confirm</button>
        </form>
        @endif
        @if($payslip->status === 'confirmed')
        <form method="POST" action="{{ route('payroll.mark-paid', $payslip->id) }}" class="inline">
            @csrf
            <button type="submit" class="px-4 py-2 text-sm bg-green-600 hover:bg-green-700 text-white rounded-xl transition font-medium">Mark as Paid</button>
        </form>
        @endif
        <button onclick="window.print()" class="px-4 py-2 text-sm border border-gray-200 dark:border-white/10 text-gray-600 dark:text-gray-300 rounded-xl hover:bg-gray-50 dark:hover:bg-white/5 transition">Print</button>
        @if($payslip->status === 'draft')
        <form method="POST" action="{{ route('payroll.delete', $payslip->id) }}" class="inline" onsubmit="return confirm('Delete this payslip?')">
            @csrf @method('DELETE')
            <button type="submit" class="px-4 py-2 text-sm border border-red-200 dark:border-red-800/50 text-red-600 dark:text-red-400 rounded-xl hover:bg-red-50 dark:hover:bg-red-900/20 transition">Delete</button>
        </form>
        @endif
    </div>
</div>

{{-- Payslip Document --}}
<div class="bg-white dark:bg-[#111118] rounded-2xl border border-gray-100 dark:border-white/5 p-8 max-w-3xl" id="payslip-doc">
    {{-- Header --}}
    <div class="flex justify-between items-start pb-6 border-b border-gray-100 dark:border-white/5">
        <div>
            <h2 class="text-2xl font-bold text-gray-900 dark:text-white">{{ $company->name ?? 'Company' }}</h2>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Payslip</p>
        </div>
        <div class="text-right">
            <p class="text-sm font-semibold text-gray-900 dark:text-white">Payslip #{{ $payslip->id }}</p>
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">
                {{ \Carbon\Carbon::parse($payslip->period_start)->format('M d') }} – {{ \Carbon\Carbon::parse($payslip->period_end)->format('M d, Y') }}
            </p>
            @if($payslip->paid_at)
            <p class="text-xs text-green-600 dark:text-green-400 mt-0.5">Paid: {{ \Carbon\Carbon::parse($payslip->paid_at)->format('M d, Y') }}</p>
            @endif
        </div>
    </div>

    {{-- Employee Info --}}
    <div class="py-5 border-b border-gray-100 dark:border-white/5 grid grid-cols-2 gap-4">
        <div>
            <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wide font-medium">Employee</p>
            <p class="text-base font-semibold text-gray-900 dark:text-white mt-1">{{ $payslip->employee_name }}</p>
            <p class="text-sm text-gray-500 dark:text-gray-400">{{ $payslip->job_title }}</p>
            @if($payslip->work_email)<p class="text-sm text-gray-500 dark:text-gray-400">{{ $payslip->work_email }}</p>@endif
        </div>
        <div>
            <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wide font-medium">Salary Structure</p>
            <p class="text-sm text-gray-900 dark:text-white mt-1">{{ $payslip->structure_name ?? '—' }}</p>
        </div>
    </div>

    {{-- Earnings --}}
    @php $earnings = $lines->where('type', 'earning'); @endphp
    @if($earnings->count())
    <div class="py-5 border-b border-gray-100 dark:border-white/5">
        <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide mb-3">Earnings</p>
        <table class="w-full text-sm">
            <tbody class="divide-y divide-gray-50 dark:divide-white/[0.03]">
                @foreach($earnings as $line)
                <tr>
                    <td class="py-2 text-gray-700 dark:text-gray-300">{{ $line->name }} <span class="text-xs text-gray-400">({{ $line->code }})</span></td>
                    <td class="py-2 text-right font-medium text-gray-900 dark:text-white">{{ number_format($line->amount, 2) }}</td>
                </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr class="border-t border-gray-200 dark:border-white/10">
                    <td class="pt-2 text-sm font-semibold text-gray-700 dark:text-gray-300">Gross Salary</td>
                    <td class="pt-2 text-right font-bold text-gray-900 dark:text-white">{{ number_format($payslip->gross_salary, 2) }}</td>
                </tr>
            </tfoot>
        </table>
    </div>
    @endif

    {{-- Deductions --}}
    @php $deductions = $lines->where('type', 'deduction'); @endphp
    @if($deductions->count())
    <div class="py-5 border-b border-gray-100 dark:border-white/5">
        <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide mb-3">Deductions</p>
        <table class="w-full text-sm">
            <tbody class="divide-y divide-gray-50 dark:divide-white/[0.03]">
                @foreach($deductions as $line)
                <tr>
                    <td class="py-2 text-gray-700 dark:text-gray-300">{{ $line->name }} <span class="text-xs text-gray-400">({{ $line->code }})</span></td>
                    <td class="py-2 text-right font-medium text-red-500 dark:text-red-400">- {{ number_format($line->amount, 2) }}</td>
                </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr class="border-t border-gray-200 dark:border-white/10">
                    <td class="pt-2 text-sm font-semibold text-gray-700 dark:text-gray-300">Total Deductions</td>
                    <td class="pt-2 text-right font-bold text-red-500 dark:text-red-400">- {{ number_format($payslip->total_deductions, 2) }}</td>
                </tr>
            </tfoot>
        </table>
    </div>
    @endif

    {{-- Net Pay --}}
    <div class="pt-5 flex justify-between items-center">
        <p class="text-lg font-bold text-gray-900 dark:text-white">Net Pay</p>
        <p class="text-2xl font-bold text-green-600 dark:text-green-400">{{ number_format($payslip->net_salary, 2) }}</p>
    </div>

    @if($payslip->note)
    <div class="mt-5 pt-5 border-t border-gray-100 dark:border-white/5">
        <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wide font-medium mb-1">Note</p>
        <p class="text-sm text-gray-600 dark:text-gray-300">{{ $payslip->note }}</p>
    </div>
    @endif
</div>

<style>
@media print {
    .no-print, nav, aside, header { display: none !important; }
    #payslip-doc { border: none !important; padding: 0 !important; }
    body { background: white !important; }
}
</style>
@endsection

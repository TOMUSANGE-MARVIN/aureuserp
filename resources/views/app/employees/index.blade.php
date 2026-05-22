@extends('app.layouts.app')
@section('title', 'Employees')
@section('breadcrumb', 'Employees')

@section('topbar_actions')
    <a href="{{ route('employees.create') }}" class="px-4 py-2 bg-brand-600 hover:bg-brand-700 text-white rounded-xl text-sm font-medium transition-colors flex items-center gap-1.5">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        New Employee
    </a>
@endsection

@section('content')
    @if(session('success'))
        <div class="flex items-center gap-3 px-4 py-3 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-xl text-green-700 dark:text-green-400 text-sm">
            <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
            {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="flex items-center gap-3 px-4 py-3 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-xl text-red-700 dark:text-red-400 text-sm">
            <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            {{ session('error') }}
        </div>
    @endif

    {{-- Stats --}}
    <div class="grid grid-cols-2 lg:grid-cols-3 gap-4">
        <div class="stat-card bg-white dark:bg-[#111118] rounded-2xl shadow-sm border border-gray-100 dark:border-white/5 p-5">
            <div class="text-2xl font-bold text-gray-900 dark:text-white">{{ number_format($total) }}</div>
            <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">Total Employees</div>
        </div>
        <div class="stat-card bg-white dark:bg-[#111118] rounded-2xl shadow-sm border border-gray-100 dark:border-white/5 p-5">
            <div class="text-2xl font-bold text-green-600 dark:text-green-400">{{ number_format($active) }}</div>
            <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">Active</div>
        </div>
        <div class="stat-card bg-white dark:bg-[#111118] rounded-2xl shadow-sm border border-gray-100 dark:border-white/5 p-5">
            <div class="text-2xl font-bold text-brand-600 dark:text-brand-400">{{ number_format($deptCount) }}</div>
            <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">Departments</div>
        </div>
    </div>

    <div class="bg-white dark:bg-[#111118] rounded-2xl shadow-sm border border-gray-100 dark:border-white/5">
        {{-- Filters & Search --}}
        <div class="p-4 border-b border-gray-100 dark:border-white/5 flex flex-col sm:flex-row gap-3 sm:items-center sm:justify-between">
            <form method="GET" action="{{ route('employees.index') }}" class="flex gap-2 flex-wrap">
                @if($departments->count())
                    <select name="department" onchange="this.form.submit()"
                            class="px-3 py-2 rounded-xl border border-gray-200 dark:border-white/10 bg-white dark:bg-[#1a1a27] text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-brand-500 text-sm">
                        <option value="">All Departments</option>
                        @foreach($departments as $dept)
                            <option value="{{ $dept->id }}" {{ $deptFilter == $dept->id ? 'selected' : '' }}>{{ $dept->name }}</option>
                        @endforeach
                    </select>
                @endif
                <input type="hidden" name="department" value="{{ $deptFilter }}">
                <input type="text" name="search" value="{{ $search }}" placeholder="Search name, email…"
                       class="flex-1 min-w-[200px] px-3 py-2 rounded-xl border border-gray-200 dark:border-white/10 bg-white dark:bg-[#1a1a27] text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-brand-500 text-sm">
                <button type="submit" class="px-4 py-2 bg-brand-600 hover:bg-brand-700 text-white rounded-xl text-sm font-medium transition-colors">Search</button>
                @if($search || $deptFilter)
                    <a href="{{ route('employees.index') }}" class="px-4 py-2 bg-gray-100 dark:bg-white/5 hover:bg-gray-200 dark:hover:bg-white/10 text-gray-700 dark:text-gray-300 rounded-xl text-sm font-medium transition-colors">Clear</a>
                @endif
            </form>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 dark:bg-white/3 border-b border-gray-100 dark:border-white/5">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Name</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Email</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Phone</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Department</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Type</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Status</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50 dark:divide-white/3">
                    @forelse($employees as $employee)
                        @php
                            $avatarColors = ['bg-brand-600','bg-blue-500','bg-green-500','bg-orange-500','bg-pink-500','bg-teal-500','bg-indigo-500'];
                            $ac = $employee->color ? 'bg-[#' . ltrim($employee->color,'#') . ']' : $avatarColors[ord(strtoupper($employee->name[0] ?? 'A')) % count($avatarColors)];
                            // Safer fallback
                            $ac = $avatarColors[ord(strtoupper($employee->name[0] ?? 'A')) % count($avatarColors)];
                        @endphp
                        <tr class="hover:bg-gray-50 dark:hover:bg-white/2 transition-colors">
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-full {{ $ac }} flex items-center justify-center text-white text-xs font-bold flex-shrink-0">
                                        {{ strtoupper(substr($employee->name ?? 'X', 0, 1)) }}
                                    </div>
                                    <div>
                                        <div class="font-medium text-gray-900 dark:text-white">{{ $employee->name }}</div>
                                        @if($employee->job_title)
                                            <div class="text-xs text-gray-400">{{ $employee->job_title }}</div>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-3 text-gray-700 dark:text-gray-300">{{ $employee->work_email ?? '—' }}</td>
                            <td class="px-4 py-3 text-gray-700 dark:text-gray-300">{{ $employee->work_phone ?? $employee->mobile_phone ?? '—' }}</td>
                            <td class="px-4 py-3 text-gray-700 dark:text-gray-300">{{ $employee->department_id ? ($deptMap[$employee->department_id] ?? '—') : '—' }}</td>
                            <td class="px-4 py-3">
                                @if($employee->employee_type)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-400">
                                        {{ ucfirst($employee->employee_type) }}
                                    </span>
                                @else
                                    <span class="text-gray-400">—</span>
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                @if($employee->is_active)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400">Active</span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 dark:bg-white/10 text-gray-600 dark:text-gray-400">Inactive</span>
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-2">
                                    <a href="{{ route('employees.show', $employee->id) }}" class="text-brand-600 dark:text-brand-400 hover:underline text-xs font-medium">View</a>
                                    <a href="{{ route('employees.edit', $employee->id) }}" class="text-gray-500 dark:text-gray-400 hover:underline text-xs font-medium">Edit</a>
                                    <form method="POST" action="{{ route('employees.destroy', $employee->id) }}" class="inline">
                                        @csrf @method('DELETE')
                                        <button type="submit" onclick="return confirm('Delete this employee?')"
                                                class="text-red-500 hover:underline text-xs font-medium">Delete</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-4 py-10 text-center text-gray-400 dark:text-gray-500">No employees found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($employees->hasPages())
            <div class="px-4 py-3 border-t border-gray-100 dark:border-white/5">
                {{ $employees->links() }}
            </div>
        @endif
    </div>
@endsection

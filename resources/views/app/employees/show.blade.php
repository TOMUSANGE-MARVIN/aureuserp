@extends('app.layouts.app')
@section('title', $employee->name . ' — Employee')
@section('breadcrumb', 'Employees')

@section('topbar_actions')
    <a href="{{ route('employees.edit', $employee->id) }}" class="px-4 py-2 bg-brand-600 hover:bg-brand-700 text-white rounded-xl text-sm font-medium transition-colors">Edit</a>
    <form method="POST" action="{{ route('employees.destroy', $employee->id) }}" class="inline">
        @csrf @method('DELETE')
        <button type="submit" onclick="return confirm('Delete this employee?')"
                class="px-4 py-2 bg-red-500 hover:bg-red-600 text-white rounded-xl text-sm font-medium transition-colors">Delete</button>
    </form>
@endsection

@section('content')
    @if(session('success'))
        <div class="flex items-center gap-3 px-4 py-3 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-xl text-green-700 dark:text-green-400 text-sm">
            <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
            {{ session('success') }}
        </div>
    @endif

    @php
        $avatarColors = ['bg-brand-600','bg-blue-500','bg-green-500','bg-orange-500','bg-pink-500','bg-teal-500','bg-indigo-500'];
        $ac = $avatarColors[ord(strtoupper($employee->name[0] ?? 'A')) % count($avatarColors)];
    @endphp

    {{-- Header --}}
    <div class="bg-white dark:bg-[#111118] rounded-2xl shadow-sm border border-gray-100 dark:border-white/5 p-6">
        <div class="flex items-center gap-5">
            <div class="w-16 h-16 rounded-2xl {{ $ac }} flex items-center justify-center text-white text-2xl font-bold flex-shrink-0">
                {{ strtoupper(substr($employee->name ?? 'X', 0, 1)) }}
            </div>
            <div class="flex-1 min-w-0">
                <h1 class="text-xl font-bold text-gray-900 dark:text-white">{{ $employee->name }}</h1>
                @if($employee->job_title)
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">{{ $employee->job_title }}</p>
                @endif
                <div class="flex flex-wrap gap-2 mt-2">
                    @if($employee->employee_type)
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-400">
                            {{ ucfirst($employee->employee_type) }}
                        </span>
                    @endif
                    @if($employee->is_active)
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400">Active</span>
                    @else
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 dark:bg-white/10 text-gray-600 dark:text-gray-400">Inactive</span>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">
        {{-- Work Info --}}
        <div class="bg-white dark:bg-[#111118] rounded-2xl shadow-sm border border-gray-100 dark:border-white/5 p-5">
            <h2 class="text-sm font-semibold text-gray-900 dark:text-white mb-4">Work Information</h2>
            <dl class="space-y-3">
                @foreach([
                    'Work Email' => $employee->work_email,
                    'Work Phone' => $employee->work_phone,
                    'Mobile Phone' => $employee->mobile_phone,
                    'Department' => $department?->name,
                ] as $label => $value)
                    <div class="flex items-start gap-3">
                        <dt class="w-28 text-xs text-gray-500 dark:text-gray-400 pt-0.5 flex-shrink-0">{{ $label }}</dt>
                        <dd class="text-sm text-gray-900 dark:text-white">
                            @if($value && $label === 'Work Email')
                                <a href="mailto:{{ $value }}" class="text-brand-600 dark:text-brand-400 hover:underline">{{ $value }}</a>
                            @else
                                {{ $value ?: '—' }}
                            @endif
                        </dd>
                    </div>
                @endforeach
            </dl>
        </div>

        {{-- Personal Info --}}
        <div class="bg-white dark:bg-[#111118] rounded-2xl shadow-sm border border-gray-100 dark:border-white/5 p-5">
            <h2 class="text-sm font-semibold text-gray-900 dark:text-white mb-4">Personal Information</h2>
            <dl class="space-y-3">
                @foreach([
                    'Gender' => $employee->gender ? ucfirst($employee->gender) : null,
                    'Birthday' => $employee->birthday ? \Carbon\Carbon::parse($employee->birthday)->format('M j, Y') : null,
                    'Marital Status' => $employee->marital ? ucfirst($employee->marital) : null,
                    'Children' => $employee->children,
                ] as $label => $value)
                    <div class="flex items-start gap-3">
                        <dt class="w-28 text-xs text-gray-500 dark:text-gray-400 pt-0.5 flex-shrink-0">{{ $label }}</dt>
                        <dd class="text-sm text-gray-900 dark:text-white">{{ $value ?: '—' }}</dd>
                    </div>
                @endforeach
            </dl>

            @if($manager)
                <h2 class="text-sm font-semibold text-gray-900 dark:text-white mb-4 mt-6">Manager</h2>
                <div class="flex items-center gap-3">
                    @php $mac = $avatarColors[ord(strtoupper($manager->name[0] ?? 'M')) % count($avatarColors)]; @endphp
                    <div class="w-8 h-8 rounded-full {{ $mac }} flex items-center justify-center text-white text-xs font-bold flex-shrink-0">
                        {{ strtoupper(substr($manager->name ?? 'M', 0, 1)) }}
                    </div>
                    <div>
                        <a href="{{ route('employees.show', $manager->id) }}" class="text-sm font-medium text-brand-600 dark:text-brand-400 hover:underline">{{ $manager->name }}</a>
                        @if($manager->job_title)
                            <div class="text-xs text-gray-400">{{ $manager->job_title }}</div>
                        @endif
                    </div>
                </div>
            @endif
        </div>
    </div>

    <div class="flex gap-3">
        <a href="{{ route('employees.index') }}" class="px-4 py-2 bg-gray-100 dark:bg-white/5 hover:bg-gray-200 dark:hover:bg-white/10 text-gray-700 dark:text-gray-300 rounded-xl text-sm font-medium transition-colors">← Back to Employees</a>
        <a href="{{ route('employees.edit', $employee->id) }}" class="px-4 py-2 bg-brand-600 hover:bg-brand-700 text-white rounded-xl text-sm font-medium transition-colors">Edit Employee</a>
    </div>
@endsection

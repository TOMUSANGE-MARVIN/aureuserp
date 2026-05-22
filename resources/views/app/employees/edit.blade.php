@extends('app.layouts.app')
@section('title', 'Edit ' . $employee->name)
@section('breadcrumb', 'Edit Employee')

@section('content')
    <div class="max-w-2xl">
        <div class="flex items-center justify-between mb-5">
            <h1 class="text-xl font-bold text-gray-900 dark:text-white">Edit: {{ $employee->name }}</h1>
            <a href="{{ route('employees.show', $employee->id) }}" class="px-4 py-2 bg-gray-100 dark:bg-white/5 hover:bg-gray-200 dark:hover:bg-white/10 text-gray-700 dark:text-gray-300 rounded-xl text-sm font-medium transition-colors">Cancel</a>
        </div>

        @if($errors->any())
            <div class="mb-5 px-4 py-3 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-xl">
                <ul class="list-disc list-inside text-sm text-red-700 dark:text-red-400 space-y-1">
                    @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('employees.update', $employee->id) }}" class="space-y-5">
            @csrf
            @method('PUT')

            <div class="bg-white dark:bg-[#111118] rounded-2xl shadow-sm border border-gray-100 dark:border-white/5 p-5 space-y-4">
                <h2 class="text-sm font-semibold text-gray-900 dark:text-white">Basic Information</h2>

                <div>
                    <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1.5">Full Name <span class="text-red-500">*</span></label>
                    <input type="text" name="name" value="{{ old('name', $employee->name) }}" required
                           class="w-full px-3 py-2 rounded-xl border border-gray-200 dark:border-white/10 bg-white dark:bg-[#1a1a27] text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-brand-500 text-sm">
                </div>

                <div>
                    <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1.5">Job Title</label>
                    <input type="text" name="job_title" value="{{ old('job_title', $employee->job_title) }}"
                           class="w-full px-3 py-2 rounded-xl border border-gray-200 dark:border-white/10 bg-white dark:bg-[#1a1a27] text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-brand-500 text-sm">
                </div>

                <div>
                    <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1.5">Employee Type</label>
                    <select name="employee_type"
                            class="w-full px-3 py-2 rounded-xl border border-gray-200 dark:border-white/10 bg-white dark:bg-[#1a1a27] text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-brand-500 text-sm">
                        <option value="">— Select Type —</option>
                        @foreach(['employee'=>'Employee','student'=>'Student','freelance'=>'Freelance','external'=>'External'] as $val => $label)
                            <option value="{{ $val }}" {{ old('employee_type', $employee->employee_type) === $val ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>

                @if($departments->count())
                    <div>
                        <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1.5">Department</label>
                        <select name="department_id"
                                class="w-full px-3 py-2 rounded-xl border border-gray-200 dark:border-white/10 bg-white dark:bg-[#1a1a27] text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-brand-500 text-sm">
                            <option value="">— Select Department —</option>
                            @foreach($departments as $dept)
                                <option value="{{ $dept->id }}" {{ old('department_id', $employee->department_id) == $dept->id ? 'selected' : '' }}>{{ $dept->name }}</option>
                            @endforeach
                        </select>
                    </div>
                @endif

                <div class="flex items-center gap-2">
                    <input type="checkbox" id="is_active" name="is_active" value="1"
                           {{ old('is_active', $employee->is_active) ? 'checked' : '' }}
                           class="w-4 h-4 rounded border-gray-300 text-brand-600 focus:ring-brand-500">
                    <label for="is_active" class="text-sm text-gray-700 dark:text-gray-300">Active</label>
                </div>
            </div>

            <div class="bg-white dark:bg-[#111118] rounded-2xl shadow-sm border border-gray-100 dark:border-white/5 p-5 space-y-4">
                <h2 class="text-sm font-semibold text-gray-900 dark:text-white">Work Contact</h2>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1.5">Work Email</label>
                        <input type="email" name="work_email" value="{{ old('work_email', $employee->work_email) }}"
                               class="w-full px-3 py-2 rounded-xl border border-gray-200 dark:border-white/10 bg-white dark:bg-[#1a1a27] text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-brand-500 text-sm">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1.5">Work Phone</label>
                        <input type="text" name="work_phone" value="{{ old('work_phone', $employee->work_phone) }}"
                               class="w-full px-3 py-2 rounded-xl border border-gray-200 dark:border-white/10 bg-white dark:bg-[#1a1a27] text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-brand-500 text-sm">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1.5">Mobile Phone</label>
                        <input type="text" name="mobile_phone" value="{{ old('mobile_phone', $employee->mobile_phone) }}"
                               class="w-full px-3 py-2 rounded-xl border border-gray-200 dark:border-white/10 bg-white dark:bg-[#1a1a27] text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-brand-500 text-sm">
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-[#111118] rounded-2xl shadow-sm border border-gray-100 dark:border-white/5 p-5 space-y-4">
                <h2 class="text-sm font-semibold text-gray-900 dark:text-white">Personal Information</h2>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1.5">Gender</label>
                        <select name="gender"
                                class="w-full px-3 py-2 rounded-xl border border-gray-200 dark:border-white/10 bg-white dark:bg-[#1a1a27] text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-brand-500 text-sm">
                            <option value="">— Select —</option>
                            @foreach(['male'=>'Male','female'=>'Female','other'=>'Other'] as $val => $label)
                                <option value="{{ $val }}" {{ old('gender', $employee->gender) === $val ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1.5">Marital Status</label>
                        <select name="marital"
                                class="w-full px-3 py-2 rounded-xl border border-gray-200 dark:border-white/10 bg-white dark:bg-[#1a1a27] text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-brand-500 text-sm">
                            <option value="">— Select —</option>
                            @foreach(['single'=>'Single','married'=>'Married','cohabitant'=>'Cohabitant','widower'=>'Widower','divorced'=>'Divorced'] as $val => $label)
                                <option value="{{ $val }}" {{ old('marital', $employee->marital) === $val ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1.5">Date of Birth</label>
                        <input type="date" name="birthday" value="{{ old('birthday', $employee->birthday ? \Carbon\Carbon::parse($employee->birthday)->format('Y-m-d') : '') }}"
                               class="w-full px-3 py-2 rounded-xl border border-gray-200 dark:border-white/10 bg-white dark:bg-[#1a1a27] text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-brand-500 text-sm">
                    </div>
                </div>
            </div>

            <div class="flex gap-3">
                <button type="submit" class="px-4 py-2 bg-brand-600 hover:bg-brand-700 text-white rounded-xl text-sm font-medium transition-colors">Save Changes</button>
                <a href="{{ route('employees.show', $employee->id) }}" class="px-4 py-2 bg-gray-100 dark:bg-white/5 hover:bg-gray-200 dark:hover:bg-white/10 text-gray-700 dark:text-gray-300 rounded-xl text-sm font-medium transition-colors">Cancel</a>
            </div>
        </form>
    </div>
@endsection

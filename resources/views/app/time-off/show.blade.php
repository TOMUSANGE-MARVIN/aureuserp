@extends('app.layouts.app')
@section('title', 'Leave Request')

@section('content')
<div class="space-y-6">
    <div class="flex items-center gap-4">
        <a href="/app/time-off" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
        </a>
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">{{ $leave->employee_name ?? 'Leave Request' }}</h1>
            <p class="text-sm text-gray-500 dark:text-gray-400">{{ $leave->leave_type_name ?? '' }}</p>
        </div>
        @php $stateColors=['draft'=>'gray','confirm'=>'yellow','validate1'=>'yellow','validate'=>'green','refuse'=>'red']; $sc=$stateColors[$leave->state]??'gray'; $stateLabels=['draft'=>'Draft','confirm'=>'Pending','validate1'=>'To Approve','validate'=>'Approved','refuse'=>'Refused']; @endphp
        <span class="ml-auto px-3 py-1 rounded-full text-sm font-medium bg-{{ $sc }}-100 text-{{ $sc }}-700 dark:bg-{{ $sc }}-900/30 dark:text-{{ $sc }}-400">{{ $stateLabels[$leave->state]??$leave->state }}</span>
    </div>

    @if(session('success'))
    <div class="bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg p-4 text-sm text-green-700 dark:text-green-400">{{ session('success') }}</div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2">
            <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm p-6">
                <h2 class="text-base font-semibold text-gray-900 dark:text-white mb-4">Leave Details</h2>
                <dl class="grid grid-cols-2 gap-4 text-sm">
                    <div><dt class="text-gray-500 dark:text-gray-400">Employee</dt><dd class="font-medium text-gray-900 dark:text-white mt-1">{{ $leave->employee_name ?? '—' }}</dd></div>
                    <div><dt class="text-gray-500 dark:text-gray-400">Leave Type</dt><dd class="font-medium text-gray-900 dark:text-white mt-1">{{ $leave->leave_type_name ?? '—' }}</dd></div>
                    <div><dt class="text-gray-500 dark:text-gray-400">From</dt><dd class="font-medium text-gray-900 dark:text-white mt-1">{{ $leave->date_from ? date('d M Y', strtotime($leave->date_from)) : '—' }}</dd></div>
                    <div><dt class="text-gray-500 dark:text-gray-400">To</dt><dd class="font-medium text-gray-900 dark:text-white mt-1">{{ $leave->date_to ? date('d M Y', strtotime($leave->date_to)) : '—' }}</dd></div>
                    <div><dt class="text-gray-500 dark:text-gray-400">Days</dt><dd class="font-medium text-gray-900 dark:text-white mt-1">{{ $leave->number_of_days ?? '—' }}</dd></div>
                    <div><dt class="text-gray-500 dark:text-gray-400">Status</dt><dd class="font-medium text-gray-900 dark:text-white mt-1">{{ $stateLabels[$leave->state]??$leave->state }}</dd></div>
                    @if($leave->description)
                    <div class="col-span-2"><dt class="text-gray-500 dark:text-gray-400">Description</dt><dd class="font-medium text-gray-900 dark:text-white mt-1">{{ $leave->description }}</dd></div>
                    @endif
                </dl>
            </div>
        </div>

        <div class="space-y-4">
            @if(in_array($leave->state, ['draft','confirm']))
            <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm p-6">
                <h2 class="text-base font-semibold text-gray-900 dark:text-white mb-4">Actions</h2>
                <div class="space-y-2">
                    <form method="POST" action="/app/time-off/{{ $leave->id }}/approve">
                        @csrf
                        <button type="submit" class="flex items-center gap-2 w-full px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg text-sm font-medium">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            Approve
                        </button>
                    </form>
                    <form method="POST" action="/app/time-off/{{ $leave->id }}/refuse">
                        @csrf
                        <button type="submit" class="flex items-center gap-2 w-full px-4 py-2 border border-red-300 dark:border-red-600 text-red-600 dark:text-red-400 rounded-lg text-sm font-medium hover:bg-red-50 dark:hover:bg-red-900/20">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                            Refuse
                        </button>
                    </form>
                    <form method="POST" action="/app/time-off/{{ $leave->id }}" onsubmit="return confirm('Delete this request?')">
                        @csrf @method('DELETE')
                        <button type="submit" class="flex items-center gap-2 w-full px-4 py-2 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-lg text-sm font-medium hover:bg-gray-50 dark:hover:bg-gray-700 mt-2">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                            Delete
                        </button>
                    </form>
                </div>
            </div>
            @endif
            <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm p-6">
                <h2 class="text-base font-semibold text-gray-900 dark:text-white mb-4">Timeline</h2>
                <dl class="space-y-3 text-sm">
                    <div><dt class="text-gray-500 dark:text-gray-400">Created</dt><dd class="font-medium text-gray-900 dark:text-white">{{ $leave->created_at ? date('d M Y', strtotime($leave->created_at)) : '—' }}</dd></div>
                    <div><dt class="text-gray-500 dark:text-gray-400">Updated</dt><dd class="font-medium text-gray-900 dark:text-white">{{ $leave->updated_at ? date('d M Y', strtotime($leave->updated_at)) : '—' }}</dd></div>
                </dl>
            </div>
        </div>
    </div>
</div>
@endsection

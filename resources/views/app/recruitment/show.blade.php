@extends('app.layouts.app')
@section('title', 'Applicant Details')

@section('content')
<div class="space-y-6">
    <div class="flex items-center gap-4">
        <a href="/app/recruitment" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
        </a>
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">{{ $applicant->candidate_name ?? 'Applicant' }}</h1>
            <p class="text-sm text-gray-500 dark:text-gray-400">{{ $applicant->stage_name ?? '' }}</p>
        </div>
        @php $stateColors=['new'=>'blue','in_progress'=>'yellow','blocked'=>'red','ready'=>'green','done'=>'brand']; $sc=$stateColors[$applicant->state]??'gray'; @endphp
        <span class="ml-auto px-3 py-1 rounded-full text-sm font-medium bg-{{ $sc }}-100 text-{{ $sc }}-700 dark:bg-{{ $sc }}-900/30 dark:text-{{ $sc }}-400 capitalize">{{ str_replace('_',' ',$applicant->state) }}</span>
    </div>

    @if(session('success'))
    <div class="bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg p-4 text-sm text-green-700 dark:text-green-400">{{ session('success') }}</div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm p-6">
                <h2 class="text-base font-semibold text-gray-900 dark:text-white mb-4">Candidate Details</h2>
                <dl class="grid grid-cols-2 gap-4 text-sm">
                    <div><dt class="text-gray-500 dark:text-gray-400">Name</dt><dd class="font-medium text-gray-900 dark:text-white mt-1">{{ $applicant->candidate_name ?? '—' }}</dd></div>
                    <div><dt class="text-gray-500 dark:text-gray-400">Email</dt><dd class="font-medium text-gray-900 dark:text-white mt-1">{{ $applicant->candidate_email ?? '—' }}</dd></div>
                    <div><dt class="text-gray-500 dark:text-gray-400">Phone</dt><dd class="font-medium text-gray-900 dark:text-white mt-1">{{ $applicant->candidate_phone ?? '—' }}</dd></div>
                    <div><dt class="text-gray-500 dark:text-gray-400">LinkedIn</dt><dd class="font-medium text-gray-900 dark:text-white mt-1">{{ $applicant->linkedin_profile ?? '—' }}</dd></div>
                    <div><dt class="text-gray-500 dark:text-gray-400">Department</dt><dd class="font-medium text-gray-900 dark:text-white mt-1">{{ $applicant->department_name ?? '—' }}</dd></div>
                    <div><dt class="text-gray-500 dark:text-gray-400">Priority</dt><dd class="font-medium text-gray-900 dark:text-white mt-1">@for($i=0;$i<($applicant->priority??0);$i++)<span class="text-yellow-400">★</span>@endfor{{ ($applicant->priority??0)==0?'Normal':'' }}</dd></div>
                    <div><dt class="text-gray-500 dark:text-gray-400">Expected Salary</dt><dd class="font-medium text-gray-900 dark:text-white mt-1">{{ $applicant->salary_expected ? number_format($applicant->salary_expected,2) : '—' }}</dd></div>
                    <div><dt class="text-gray-500 dark:text-gray-400">Proposed Salary</dt><dd class="font-medium text-gray-900 dark:text-white mt-1">{{ $applicant->salary_proposed ? number_format($applicant->salary_proposed,2) : '—' }}</dd></div>
                </dl>
            </div>
        </div>

        <div class="space-y-4">
            <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm p-6">
                <h2 class="text-base font-semibold text-gray-900 dark:text-white mb-4">Actions</h2>
                <div class="space-y-2">
                    <a href="/app/recruitment/{{ $applicant->id }}/edit" class="flex items-center gap-2 w-full px-4 py-2 bg-brand-600 hover:bg-brand-700 text-white rounded-lg text-sm font-medium">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                        Edit Application
                    </a>
                    <form method="POST" action="/app/recruitment/{{ $applicant->id }}" onsubmit="return confirm('Delete this application?')">
                        @csrf @method('DELETE')
                        <button type="submit" class="flex items-center gap-2 w-full px-4 py-2 border border-red-300 dark:border-red-600 text-red-600 dark:text-red-400 rounded-lg text-sm font-medium hover:bg-red-50 dark:hover:bg-red-900/20">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                            Delete
                        </button>
                    </form>
                </div>
            </div>
            <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm p-6">
                <h2 class="text-base font-semibold text-gray-900 dark:text-white mb-4">Timeline</h2>
                <dl class="space-y-3 text-sm">
                    <div><dt class="text-gray-500 dark:text-gray-400">Applied</dt><dd class="font-medium text-gray-900 dark:text-white">{{ $applicant->date_opened ? date('d M Y', strtotime($applicant->date_opened)) : ($applicant->created_at ? date('d M Y', strtotime($applicant->created_at)) : '—') }}</dd></div>
                    <div><dt class="text-gray-500 dark:text-gray-400">Last Updated</dt><dd class="font-medium text-gray-900 dark:text-white">{{ $applicant->updated_at ? date('d M Y', strtotime($applicant->updated_at)) : '—' }}</dd></div>
                </dl>
            </div>
        </div>
    </div>
</div>
@endsection

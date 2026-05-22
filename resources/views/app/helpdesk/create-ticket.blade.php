@extends('app.layouts.app')
@section('title', 'New Ticket')
@section('breadcrumb', 'Helpdesk / New Ticket')

@section('content')
<div class="flex items-center justify-between">
    <h1 class="text-xl font-bold text-gray-900 dark:text-white">New Support Ticket</h1>
    <a href="{{ route('helpdesk.index') }}" class="text-sm text-gray-500 dark:text-gray-400 hover:underline">← Helpdesk</a>
</div>

@if($errors->any())
<div class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-xl p-4 text-sm text-red-700 dark:text-red-300">
    <ul class="list-disc pl-4 space-y-1">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
</div>
@endif

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <div class="lg:col-span-2">
        <div class="bg-white dark:bg-[#111118] rounded-2xl border border-gray-100 dark:border-white/5 p-6">
            <form method="POST" action="{{ route('helpdesk.store') }}" class="space-y-5">
                @csrf
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Subject / Title *</label>
                    <input type="text" name="title" value="{{ old('title') }}" required placeholder="Describe the issue briefly…"
                           class="w-full px-3 py-2 text-sm border border-gray-200 dark:border-white/10 rounded-xl bg-white dark:bg-[#111118] text-gray-900 dark:text-white">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Description</label>
                    <textarea name="description" rows="6" placeholder="Full description of the issue…"
                              class="w-full px-3 py-2 text-sm border border-gray-200 dark:border-white/10 rounded-xl bg-white dark:bg-[#111118] text-gray-900 dark:text-white resize-none">{{ old('description') }}</textarea>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Customer Name</label>
                        <input type="text" name="customer_name" value="{{ old('customer_name') }}"
                               class="w-full px-3 py-2 text-sm border border-gray-200 dark:border-white/10 rounded-xl bg-white dark:bg-[#111118] text-gray-900 dark:text-white">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Customer Email</label>
                        <input type="email" name="customer_email" value="{{ old('customer_email') }}"
                               class="w-full px-3 py-2 text-sm border border-gray-200 dark:border-white/10 rounded-xl bg-white dark:bg-[#111118] text-gray-900 dark:text-white">
                    </div>
                </div>
                <div class="flex gap-3 pt-2">
                    <button type="submit" class="px-5 py-2 text-sm bg-brand-600 hover:bg-brand-700 text-white rounded-xl transition font-medium">Create Ticket</button>
                    <a href="{{ route('helpdesk.index') }}" class="px-5 py-2 text-sm border border-gray-200 dark:border-white/10 text-gray-600 dark:text-gray-300 rounded-xl hover:bg-gray-50 dark:hover:bg-white/5 transition">Cancel</a>
                </div>
            </form>
        </div>
    </div>
    <div class="space-y-4">
        <div class="bg-white dark:bg-[#111118] rounded-2xl border border-gray-100 dark:border-white/5 p-5">
            <h3 class="text-sm font-semibold text-gray-900 dark:text-white mb-4">Ticket Details</h3>
            <form method="POST" action="{{ route('helpdesk.store') }}" id="details-form" class="space-y-4">
                {{-- This is rendered alongside the main form via JS submission --}}
            </form>
            <div class="space-y-4">
                <div>
                    <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Priority</label>
                    <select name="priority" form="main-form" id="priority-select"
                            class="w-full px-3 py-2 text-sm border border-gray-200 dark:border-white/10 rounded-xl bg-white dark:bg-[#111118] text-gray-900 dark:text-white">
                        <option value="low">Low</option>
                        <option value="medium" selected>Medium</option>
                        <option value="high">High</option>
                        <option value="urgent">Urgent</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Team</label>
                    <select name="team_id" form="main-form"
                            class="w-full px-3 py-2 text-sm border border-gray-200 dark:border-white/10 rounded-xl bg-white dark:bg-[#111118] text-gray-900 dark:text-white">
                        <option value="">No team</option>
                        @foreach($teams as $team)<option value="{{ $team->id }}">{{ $team->name }}</option>@endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Assign To</label>
                    <select name="assigned_to" form="main-form"
                            class="w-full px-3 py-2 text-sm border border-gray-200 dark:border-white/10 rounded-xl bg-white dark:bg-[#111118] text-gray-900 dark:text-white">
                        <option value="">Unassigned</option>
                        @foreach($users as $u)<option value="{{ $u->id }}">{{ $u->name }}</option>@endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Type</label>
                    <input type="text" name="type" form="main-form" placeholder="e.g. Bug, Feature, Question…"
                           class="w-full px-3 py-2 text-sm border border-gray-200 dark:border-white/10 rounded-xl bg-white dark:bg-[#111118] text-gray-900 dark:text-white">
                </div>
            </div>
        </div>
    </div>
</div>
{{-- Give main form an id so sidebar fields can reference it --}}
<script>document.querySelector('form[action="{{ route('helpdesk.store') }}"]').id='main-form';</script>
@endsection

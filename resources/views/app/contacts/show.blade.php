@extends('app.layouts.app')
@section('title', $contact->name . ' — Contact')
@section('breadcrumb', 'Contacts')

@section('topbar_actions')
    <a href="{{ route('contacts.edit', $contact->id) }}" class="px-4 py-2 bg-brand-600 hover:bg-brand-700 text-white rounded-xl text-sm font-medium transition-colors">Edit</a>
    <form method="POST" action="{{ route('contacts.destroy', $contact->id) }}" class="inline">
        @csrf @method('DELETE')
        <button type="submit" onclick="return confirm('Delete this contact?')"
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

    {{-- Header --}}
    @php
        $colors = ['bg-brand-600','bg-blue-500','bg-green-500','bg-orange-500','bg-pink-500','bg-teal-500'];
        $colorClass = $colors[ord(strtoupper($contact->name[0] ?? 'A')) % count($colors)];
    @endphp
    <div class="bg-white dark:bg-[#111118] rounded-2xl shadow-sm border border-gray-100 dark:border-white/5 p-6">
        <div class="flex items-center gap-5">
            <div class="w-16 h-16 rounded-2xl {{ $colorClass }} flex items-center justify-center text-white text-2xl font-bold flex-shrink-0">
                {{ strtoupper(substr($contact->name ?? 'X', 0, 1)) }}
            </div>
            <div class="flex-1 min-w-0">
                <h1 class="text-xl font-bold text-gray-900 dark:text-white">{{ $contact->name }}</h1>
                @if($contact->job_title)
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">{{ $contact->job_title }}</p>
                @endif
                <div class="flex flex-wrap gap-2 mt-2">
                    @if($contact->account_type === 'company')
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-400">Company</span>
                    @else
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 dark:bg-white/10 text-gray-600 dark:text-gray-400">Individual</span>
                    @endif
                    @if(($contact->customer_rank ?? 0) > 0)
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400">Customer</span>
                    @endif
                    @if(($contact->supplier_rank ?? 0) > 0)
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-orange-100 dark:bg-orange-900/30 text-orange-700 dark:text-orange-400">Supplier</span>
                    @endif
                    @if($contact->is_active)
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400">Active</span>
                    @else
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 dark:bg-white/10 text-gray-600 dark:text-gray-400">Inactive</span>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- Detail cards --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">
        {{-- Contact Info --}}
        <div class="bg-white dark:bg-[#111118] rounded-2xl shadow-sm border border-gray-100 dark:border-white/5 p-5">
            <h2 class="text-sm font-semibold text-gray-900 dark:text-white mb-4">Contact Information</h2>
            <dl class="space-y-3">
                @foreach([
                    'Email' => $contact->email,
                    'Phone' => $contact->phone,
                    'Mobile' => $contact->mobile,
                    'Job Title' => $contact->job_title,
                    'Website' => $contact->website,
                ] as $label => $value)
                    <div class="flex items-start gap-3">
                        <dt class="w-24 text-xs text-gray-500 dark:text-gray-400 pt-0.5 flex-shrink-0">{{ $label }}</dt>
                        <dd class="text-sm text-gray-900 dark:text-white flex-1">
                            @if($value)
                                @if($label === 'Email')
                                    <a href="mailto:{{ $value }}" class="text-brand-600 dark:text-brand-400 hover:underline">{{ $value }}</a>
                                @elseif($label === 'Website')
                                    <a href="{{ $value }}" target="_blank" class="text-brand-600 dark:text-brand-400 hover:underline">{{ $value }}</a>
                                @else
                                    {{ $value }}
                                @endif
                            @else
                                <span class="text-gray-400">—</span>
                            @endif
                        </dd>
                    </div>
                @endforeach
            </dl>
        </div>

        {{-- Address --}}
        <div class="bg-white dark:bg-[#111118] rounded-2xl shadow-sm border border-gray-100 dark:border-white/5 p-5">
            <h2 class="text-sm font-semibold text-gray-900 dark:text-white mb-4">Address</h2>
            <dl class="space-y-3">
                @foreach([
                    'Street 1' => $contact->street1,
                    'Street 2' => $contact->street2,
                    'City' => $contact->city,
                    'ZIP' => $contact->zip,
                ] as $label => $value)
                    <div class="flex items-start gap-3">
                        <dt class="w-24 text-xs text-gray-500 dark:text-gray-400 pt-0.5 flex-shrink-0">{{ $label }}</dt>
                        <dd class="text-sm text-gray-900 dark:text-white">{{ $value ?: '—' }}</dd>
                    </div>
                @endforeach
            </dl>

            <h2 class="text-sm font-semibold text-gray-900 dark:text-white mb-4 mt-6">Additional Info</h2>
            <dl class="space-y-3">
                <div class="flex items-start gap-3">
                    <dt class="w-24 text-xs text-gray-500 dark:text-gray-400 pt-0.5 flex-shrink-0">Customer Rank</dt>
                    <dd class="text-sm text-gray-900 dark:text-white">{{ $contact->customer_rank ?? 0 }}</dd>
                </div>
                <div class="flex items-start gap-3">
                    <dt class="w-24 text-xs text-gray-500 dark:text-gray-400 pt-0.5 flex-shrink-0">Supplier Rank</dt>
                    <dd class="text-sm text-gray-900 dark:text-white">{{ $contact->supplier_rank ?? 0 }}</dd>
                </div>
                <div class="flex items-start gap-3">
                    <dt class="w-24 text-xs text-gray-500 dark:text-gray-400 pt-0.5 flex-shrink-0">Created</dt>
                    <dd class="text-sm text-gray-900 dark:text-white">{{ $contact->created_at ? \Carbon\Carbon::parse($contact->created_at)->format('M j, Y') : '—' }}</dd>
                </div>
            </dl>
        </div>
    </div>

    <div class="flex gap-3">
        <a href="{{ route('contacts.index') }}" class="px-4 py-2 bg-gray-100 dark:bg-white/5 hover:bg-gray-200 dark:hover:bg-white/10 text-gray-700 dark:text-gray-300 rounded-xl text-sm font-medium transition-colors">← Back to Contacts</a>
        <a href="{{ route('contacts.edit', $contact->id) }}" class="px-4 py-2 bg-brand-600 hover:bg-brand-700 text-white rounded-xl text-sm font-medium transition-colors">Edit Contact</a>
    </div>
@endsection

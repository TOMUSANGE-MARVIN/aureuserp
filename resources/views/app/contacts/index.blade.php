@extends('app.layouts.app')
@section('title', 'Contacts')
@section('breadcrumb', 'Contacts')

@section('topbar_actions')
    <a href="{{ route('contacts.create') }}" class="px-4 py-2 bg-brand-600 hover:bg-brand-700 text-white rounded-xl text-sm font-medium transition-colors flex items-center gap-1.5">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        New Contact
    </a>
@endsection

@section('content')
    {{-- Flash messages --}}
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
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="stat-card bg-white dark:bg-[#111118] rounded-2xl shadow-sm border border-gray-100 dark:border-white/5 p-5">
            <div class="text-2xl font-bold text-gray-900 dark:text-white">{{ number_format($total) }}</div>
            <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">Total Contacts</div>
        </div>
        <div class="stat-card bg-white dark:bg-[#111118] rounded-2xl shadow-sm border border-gray-100 dark:border-white/5 p-5">
            <div class="text-2xl font-bold text-blue-600 dark:text-blue-400">{{ number_format($customers) }}</div>
            <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">Customers</div>
        </div>
        <div class="stat-card bg-white dark:bg-[#111118] rounded-2xl shadow-sm border border-gray-100 dark:border-white/5 p-5">
            <div class="text-2xl font-bold text-green-600 dark:text-green-400">{{ number_format($suppliers) }}</div>
            <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">Suppliers</div>
        </div>
        <div class="stat-card bg-white dark:bg-[#111118] rounded-2xl shadow-sm border border-gray-100 dark:border-white/5 p-5">
            <div class="text-2xl font-bold text-brand-600 dark:text-brand-400">{{ number_format($companies) }}</div>
            <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">Companies</div>
        </div>
    </div>

    {{-- Main card --}}
    <div class="bg-white dark:bg-[#111118] rounded-2xl shadow-sm border border-gray-100 dark:border-white/5">
        {{-- Filters & Search --}}
        <div class="p-4 border-b border-gray-100 dark:border-white/5 flex flex-col sm:flex-row gap-3 sm:items-center sm:justify-between">
            <div class="flex gap-1">
                @foreach(['all' => 'All', 'customer' => 'Customers', 'supplier' => 'Suppliers', 'company' => 'Companies'] as $val => $label)
                    <a href="{{ route('contacts.index', array_merge(request()->query(), ['filter' => $val])) }}"
                       class="px-3 py-1.5 text-sm rounded-lg font-medium transition-colors
                              {{ $filter === $val ? 'bg-brand-600 text-white' : 'text-gray-500 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-white/5' }}">
                        {{ $label }}
                    </a>
                @endforeach
            </div>
            <form method="GET" action="{{ route('contacts.index') }}" class="flex gap-2">
                <input type="hidden" name="filter" value="{{ $filter }}">
                <input type="text" name="search" value="{{ $search }}" placeholder="Search name, email…"
                       class="w-full px-3 py-2 rounded-xl border border-gray-200 dark:border-white/10 bg-white dark:bg-[#1a1a27] text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-brand-500 text-sm">
                <button type="submit" class="px-4 py-2 bg-brand-600 hover:bg-brand-700 text-white rounded-xl text-sm font-medium transition-colors">Search</button>
                @if($search)
                    <a href="{{ route('contacts.index', ['filter' => $filter]) }}" class="px-4 py-2 bg-gray-100 dark:bg-white/5 hover:bg-gray-200 dark:hover:bg-white/10 text-gray-700 dark:text-gray-300 rounded-xl text-sm font-medium transition-colors">Clear</a>
                @endif
            </form>
        </div>

        {{-- Table --}}
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 dark:bg-white/3 border-b border-gray-100 dark:border-white/5">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Name</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Email</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Phone</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Type</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">City</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Status</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50 dark:divide-white/3">
                    @forelse($contacts as $contact)
                        @php
                            $colors = ['bg-brand-600','bg-blue-500','bg-green-500','bg-orange-500','bg-pink-500','bg-teal-500'];
                            $colorClass = $colors[ord(strtoupper($contact->name[0] ?? 'A')) % count($colors)];
                        @endphp
                        <tr class="hover:bg-gray-50 dark:hover:bg-white/2 transition-colors">
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-full {{ $colorClass }} flex items-center justify-center text-white text-xs font-bold flex-shrink-0">
                                        {{ strtoupper(substr($contact->name ?? 'X', 0, 1)) }}
                                    </div>
                                    <div>
                                        <div class="font-medium text-gray-900 dark:text-white">{{ $contact->name }}</div>
                                        @if($contact->job_title)
                                            <div class="text-xs text-gray-400">{{ $contact->job_title }}</div>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-3 text-gray-700 dark:text-gray-300">{{ $contact->email ?? '—' }}</td>
                            <td class="px-4 py-3 text-gray-700 dark:text-gray-300">{{ $contact->phone ?? $contact->mobile ?? '—' }}</td>
                            <td class="px-4 py-3">
                                @if($contact->account_type === 'company')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-400">Company</span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 dark:bg-white/10 text-gray-600 dark:text-gray-400">Individual</span>
                                @endif
                                @if(($contact->customer_rank ?? 0) > 0)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400 ml-1">Customer</span>
                                @endif
                                @if(($contact->supplier_rank ?? 0) > 0)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-orange-100 dark:bg-orange-900/30 text-orange-700 dark:text-orange-400 ml-1">Supplier</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-gray-700 dark:text-gray-300">{{ $contact->city ?? '—' }}</td>
                            <td class="px-4 py-3">
                                @if($contact->is_active)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400">Active</span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 dark:bg-white/10 text-gray-600 dark:text-gray-400">Inactive</span>
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-2">
                                    <a href="{{ route('contacts.show', $contact->id) }}" class="text-brand-600 dark:text-brand-400 hover:underline text-xs font-medium">View</a>
                                    <a href="{{ route('contacts.edit', $contact->id) }}" class="text-gray-500 dark:text-gray-400 hover:underline text-xs font-medium">Edit</a>
                                    <form method="POST" action="{{ route('contacts.destroy', $contact->id) }}" class="inline">
                                        @csrf @method('DELETE')
                                        <button type="submit" onclick="return confirm('Delete this contact?')"
                                                class="text-red-500 hover:underline text-xs font-medium">Delete</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-4 py-10 text-center text-gray-400 dark:text-gray-500">
                                No contacts found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if($contacts->hasPages())
            <div class="px-4 py-3 border-t border-gray-100 dark:border-white/5">
                {{ $contacts->links() }}
            </div>
        @endif
    </div>
@endsection

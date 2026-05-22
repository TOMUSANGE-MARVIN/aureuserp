@extends('app.layouts.app')
@section('title', 'Website Pages')
@section('breadcrumb', 'Website')

@section('topbar_actions')
    <a href="{{ route('website.create') }}" class="flex items-center gap-2 px-3 py-1.5 bg-brand-600 hover:bg-brand-700 text-white rounded-xl text-sm font-medium transition-colors">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        New Page
    </a>
@endsection

@section('content')
    {{-- Stats --}}
    <div class="grid grid-cols-3 gap-4">
        <div class="stat-card bg-white dark:bg-[#111118] rounded-2xl border border-gray-100 dark:border-white/5 p-4">
            <p class="text-xs text-gray-500 dark:text-gray-400 font-medium uppercase tracking-wide">Total Pages</p>
            <p class="text-2xl font-bold text-gray-900 dark:text-white mt-1">{{ $total }}</p>
        </div>
        <div class="stat-card bg-white dark:bg-[#111118] rounded-2xl border border-gray-100 dark:border-white/5 p-4">
            <p class="text-xs text-gray-500 dark:text-gray-400 font-medium uppercase tracking-wide">Published</p>
            <p class="text-2xl font-bold text-green-600 dark:text-green-400 mt-1">{{ $published }}</p>
        </div>
        <div class="stat-card bg-white dark:bg-[#111118] rounded-2xl border border-gray-100 dark:border-white/5 p-4">
            <p class="text-xs text-gray-500 dark:text-gray-400 font-medium uppercase tracking-wide">Drafts</p>
            <p class="text-2xl font-bold text-yellow-600 dark:text-yellow-400 mt-1">{{ $drafts }}</p>
        </div>
    </div>

    {{-- Filters --}}
    <div class="flex flex-wrap items-center gap-3">
        <form method="GET" action="{{ route('website.index') }}" class="flex-1 max-w-sm">
            <input type="text" name="search" value="{{ $search }}" placeholder="Search pages..."
                   class="w-full px-3 py-2 rounded-xl border border-gray-200 dark:border-white/10 bg-white dark:bg-[#111118] text-gray-900 dark:text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-brand-500 text-sm">
        </form>
        <div class="flex gap-1 bg-gray-100 dark:bg-white/5 rounded-xl p-1">
            @foreach(['all'=>'All','published'=>'Published','draft'=>'Draft'] as $k=>$label)
                <a href="{{ route('website.index', ['filter'=>$k,'search'=>$search]) }}"
                   class="px-3 py-1 rounded-lg text-sm font-medium transition-colors {{ $filter===$k ? 'bg-white dark:bg-white/10 text-brand-600 dark:text-brand-400 shadow-sm' : 'text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300' }}">
                    {{ $label }}
                </a>
            @endforeach
        </div>
    </div>

    @if(session('success'))
        <div class="px-4 py-3 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-xl text-sm text-green-700 dark:text-green-400">{{ session('success') }}</div>
    @endif

    {{-- Table --}}
    <div class="bg-white dark:bg-[#111118] rounded-2xl border border-gray-100 dark:border-white/5 overflow-hidden">
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b border-gray-100 dark:border-white/5">
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide">Title</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide">Slug</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide">Status</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide">Published</th>
                    <th class="px-4 py-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50 dark:divide-white/5">
                @forelse($pages as $page)
                    <tr class="hover:bg-gray-50 dark:hover:bg-white/2 transition-colors">
                        <td class="px-4 py-3 font-medium text-gray-900 dark:text-white">{{ $page->title }}</td>
                        <td class="px-4 py-3 text-gray-500 dark:text-gray-400 font-mono text-xs">/{{ $page->slug }}</td>
                        <td class="px-4 py-3">
                            <span class="inline-flex px-2 py-0.5 rounded-full text-xs font-medium {{ $page->is_published ? 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400' : 'bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-400' }}">
                                {{ $page->is_published ? 'Published' : 'Draft' }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-gray-500 dark:text-gray-400 text-xs">{{ $page->published_at ? \Carbon\Carbon::parse($page->published_at)->format('M d, Y') : '—' }}</td>
                        <td class="px-4 py-3 text-right">
                            <div class="flex items-center justify-end gap-2">
                                <form method="POST" action="{{ route('website.toggle-publish', $page->id) }}">
                                    @csrf
                                    <button type="submit" class="px-2 py-1 rounded-lg text-xs font-medium {{ $page->is_published ? 'bg-yellow-100 text-yellow-700 hover:bg-yellow-200' : 'bg-green-100 text-green-700 hover:bg-green-200' }} transition-colors">
                                        {{ $page->is_published ? 'Unpublish' : 'Publish' }}
                                    </button>
                                </form>
                                <a href="{{ route('website.show', $page->id) }}" class="px-2 py-1 rounded-lg bg-gray-100 dark:bg-white/5 hover:bg-gray-200 dark:hover:bg-white/10 text-gray-600 dark:text-gray-400 text-xs transition-colors">View</a>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="px-4 py-8 text-center text-gray-400 dark:text-gray-500">No pages found.</td></tr>
                @endforelse
            </tbody>
        </table>
        @if($pages->hasPages())
            <div class="px-4 py-3 border-t border-gray-100 dark:border-white/5">{{ $pages->links() }}</div>
        @endif
    </div>
@endsection

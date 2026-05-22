@extends('app.layouts.app')
@section('title', $page->title)
@section('breadcrumb', 'Website')

@section('content')
    <div class="max-w-3xl">
        <div class="flex items-center justify-between mb-5">
            <div>
                <h1 class="text-xl font-bold text-gray-900 dark:text-white">{{ $page->title }}</h1>
                <p class="text-sm text-gray-500 mt-1 font-mono">/{{ $page->slug }}</p>
            </div>
            <div class="flex gap-2">
                <form method="POST" action="{{ route('website.toggle-publish', $page->id) }}">
                    @csrf
                    <button type="submit" class="px-4 py-2 rounded-xl text-sm font-medium transition-colors {{ $page->is_published ? 'bg-yellow-100 text-yellow-700 hover:bg-yellow-200' : 'bg-green-100 text-green-700 hover:bg-green-200' }}">
                        {{ $page->is_published ? 'Unpublish' : 'Publish' }}
                    </button>
                </form>
                <a href="{{ route('website.index') }}" class="px-4 py-2 bg-gray-100 dark:bg-white/5 hover:bg-gray-200 dark:hover:bg-white/10 text-gray-700 dark:text-gray-300 rounded-xl text-sm font-medium transition-colors">Back</a>
            </div>
        </div>

        @if(session('success'))
            <div class="mb-4 px-4 py-3 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-xl text-sm text-green-700 dark:text-green-400">{{ session('success') }}</div>
        @endif

        <div class="bg-white dark:bg-[#111118] rounded-2xl border border-gray-100 dark:border-white/5 p-5 space-y-4">
            <div class="flex items-center gap-2">
                <span class="inline-flex px-2 py-0.5 rounded-full text-xs font-medium {{ $page->is_published ? 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400' : 'bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-400' }}">
                    {{ $page->is_published ? 'Published' : 'Draft' }}
                </span>
                @if($page->published_at)
                    <span class="text-xs text-gray-400">{{ \Carbon\Carbon::parse($page->published_at)->format('M d, Y') }}</span>
                @endif
            </div>

            @if($page->content)
                <div class="prose dark:prose-invert max-w-none text-sm text-gray-700 dark:text-gray-300 border-t border-gray-100 dark:border-white/5 pt-4">
                    {!! nl2br(e($page->content)) !!}
                </div>
            @endif

            @if($page->meta_description)
                <div class="border-t border-gray-100 dark:border-white/5 pt-4">
                    <p class="text-xs text-gray-500 mb-1 font-medium">Meta Description</p>
                    <p class="text-sm text-gray-700 dark:text-gray-300">{{ $page->meta_description }}</p>
                </div>
            @endif
        </div>

        <form method="POST" action="{{ route('website.destroy', $page->id) }}" class="mt-4" onsubmit="return confirm('Delete this page?')">
            @csrf
            <button type="submit" class="px-4 py-2 bg-red-100 hover:bg-red-200 text-red-700 rounded-xl text-sm font-medium transition-colors">Delete Page</button>
        </form>
    </div>
@endsection

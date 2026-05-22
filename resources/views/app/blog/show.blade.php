@extends('app.layouts.app')
@section('title', $post->title)
@section('breadcrumb', 'Blog')

@section('content')
    <div class="max-w-3xl">
        <div class="flex items-center justify-between mb-5">
            <div>
                <h1 class="text-xl font-bold text-gray-900 dark:text-white">{{ $post->title }}</h1>
                @if($post->sub_title)<p class="text-sm text-gray-500 mt-1">{{ $post->sub_title }}</p>@endif
            </div>
            <div class="flex gap-2">
                <form method="POST" action="{{ route('blog.toggle-publish', $post->id) }}">
                    @csrf
                    <button type="submit" class="px-4 py-2 rounded-xl text-sm font-medium transition-colors {{ $post->is_published ? 'bg-yellow-100 text-yellow-700 hover:bg-yellow-200' : 'bg-green-100 text-green-700 hover:bg-green-200' }}">
                        {{ $post->is_published ? 'Unpublish' : 'Publish' }}
                    </button>
                </form>
                <a href="{{ route('blog.index') }}" class="px-4 py-2 bg-gray-100 dark:bg-white/5 hover:bg-gray-200 dark:hover:bg-white/10 text-gray-700 dark:text-gray-300 rounded-xl text-sm font-medium transition-colors">Back</a>
            </div>
        </div>

        @if(session('success'))
            <div class="mb-4 px-4 py-3 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-xl text-sm text-green-700 dark:text-green-400">{{ session('success') }}</div>
        @endif

        <div class="bg-white dark:bg-[#111118] rounded-2xl border border-gray-100 dark:border-white/5 p-5 space-y-4">
            <div class="flex items-center gap-3 flex-wrap">
                <span class="inline-flex px-2 py-0.5 rounded-full text-xs font-medium {{ $post->is_published ? 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400' : 'bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-400' }}">
                    {{ $post->is_published ? 'Published' : 'Draft' }}
                </span>
                @if($post->author_name)<span class="text-xs text-gray-500">By {{ $post->author_name }}</span>@endif
                @if($cat)<span class="text-xs text-brand-500 bg-brand-50 dark:bg-brand-900/20 px-2 py-0.5 rounded-full">{{ $cat->name }}</span>@endif
                <span class="text-xs text-gray-400">{{ number_format($post->visits ?? 0) }} visits</span>
            </div>

            @if($post->content)
                <div class="text-sm text-gray-700 dark:text-gray-300 border-t border-gray-100 dark:border-white/5 pt-4 whitespace-pre-line">{{ $post->content }}</div>
            @endif

            @if($post->meta_description)
                <div class="border-t border-gray-100 dark:border-white/5 pt-4">
                    <p class="text-xs text-gray-500 mb-1 font-medium">Meta Description</p>
                    <p class="text-sm text-gray-700 dark:text-gray-300">{{ $post->meta_description }}</p>
                </div>
            @endif
        </div>

        <form method="POST" action="{{ route('blog.destroy', $post->id) }}" class="mt-4" onsubmit="return confirm('Delete this post?')">
            @csrf
            <button type="submit" class="px-4 py-2 bg-red-100 hover:bg-red-200 text-red-700 rounded-xl text-sm font-medium transition-colors">Delete Post</button>
        </form>
    </div>
@endsection

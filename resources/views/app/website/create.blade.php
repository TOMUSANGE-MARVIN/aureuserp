@extends('app.layouts.app')
@section('title', 'New Page')
@section('breadcrumb', 'Website')

@section('content')
    <div class="max-w-2xl">
        <div class="flex items-center justify-between mb-5">
            <h1 class="text-xl font-bold text-gray-900 dark:text-white">New Page</h1>
            <a href="{{ route('website.index') }}" class="px-4 py-2 bg-gray-100 dark:bg-white/5 hover:bg-gray-200 dark:hover:bg-white/10 text-gray-700 dark:text-gray-300 rounded-xl text-sm font-medium transition-colors">Cancel</a>
        </div>

        @if($errors->any())
            <div class="mb-5 px-4 py-3 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-xl">
                <ul class="list-disc list-inside text-sm text-red-700 dark:text-red-400 space-y-1">
                    @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('website.store') }}" class="space-y-5">
            @csrf
            <div class="bg-white dark:bg-[#111118] rounded-2xl border border-gray-100 dark:border-white/5 p-5 space-y-4">
                <div>
                    <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1.5">Title <span class="text-red-500">*</span></label>
                    <input type="text" name="title" value="{{ old('title') }}" required
                           class="w-full px-3 py-2 rounded-xl border border-gray-200 dark:border-white/10 bg-white dark:bg-[#1a1a27] text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-brand-500 text-sm">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1.5">Slug <span class="text-red-500">*</span></label>
                    <input type="text" name="slug" value="{{ old('slug') }}" required placeholder="my-page-url"
                           class="w-full px-3 py-2 rounded-xl border border-gray-200 dark:border-white/10 bg-white dark:bg-[#1a1a27] text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-brand-500 text-sm font-mono">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1.5">Content</label>
                    <textarea name="content" rows="6"
                              class="w-full px-3 py-2 rounded-xl border border-gray-200 dark:border-white/10 bg-white dark:bg-[#1a1a27] text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-brand-500 text-sm">{{ old('content') }}</textarea>
                </div>
                <div class="flex items-center gap-2">
                    <input type="checkbox" id="is_published" name="is_published" value="1" {{ old('is_published') ? 'checked' : '' }}
                           class="w-4 h-4 rounded border-gray-300 text-brand-600 focus:ring-brand-500">
                    <label for="is_published" class="text-sm text-gray-700 dark:text-gray-300">Publish immediately</label>
                </div>
            </div>

            <div class="bg-white dark:bg-[#111118] rounded-2xl border border-gray-100 dark:border-white/5 p-5 space-y-4">
                <h2 class="text-sm font-semibold text-gray-900 dark:text-white">SEO</h2>
                <div>
                    <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1.5">Meta Title</label>
                    <input type="text" name="meta_title" value="{{ old('meta_title') }}"
                           class="w-full px-3 py-2 rounded-xl border border-gray-200 dark:border-white/10 bg-white dark:bg-[#1a1a27] text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-brand-500 text-sm">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1.5">Meta Description</label>
                    <textarea name="meta_description" rows="2"
                              class="w-full px-3 py-2 rounded-xl border border-gray-200 dark:border-white/10 bg-white dark:bg-[#1a1a27] text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-brand-500 text-sm">{{ old('meta_description') }}</textarea>
                </div>
            </div>

            <div class="flex gap-3">
                <button type="submit" class="px-4 py-2 bg-brand-600 hover:bg-brand-700 text-white rounded-xl text-sm font-medium transition-colors">Create Page</button>
                <a href="{{ route('website.index') }}" class="px-4 py-2 bg-gray-100 dark:bg-white/5 hover:bg-gray-200 dark:hover:bg-white/10 text-gray-700 dark:text-gray-300 rounded-xl text-sm font-medium transition-colors">Cancel</a>
            </div>
        </form>
    </div>
@endsection

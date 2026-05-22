<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BlogController extends Controller
{
    public function index(Request $request)
    {
        $search   = $request->input('search', '');
        $filter   = $request->input('filter', 'all');
        $category = $request->input('category', '');

        $query = DB::table('blogs_posts')->whereNull('deleted_at');

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('author_name', 'like', "%{$search}%");
            });
        }

        if ($filter === 'published') {
            $query->where('is_published', 1);
        } elseif ($filter === 'draft') {
            $query->where('is_published', 0);
        }

        if ($category) {
            $query->where('category_id', $category);
        }

        $posts = $query->orderByDesc('created_at')->paginate(20)->withQueryString();

        $categories = DB::table('blogs_categories')->orderBy('name')->get();
        $total      = DB::table('blogs_posts')->whereNull('deleted_at')->count();
        $published  = DB::table('blogs_posts')->whereNull('deleted_at')->where('is_published', 1)->count();
        $drafts     = $total - $published;

        return view('app.blog.index', compact('posts', 'search', 'filter', 'category', 'categories', 'total', 'published', 'drafts'));
    }

    public function show($id)
    {
        $post = DB::table('blogs_posts')->whereNull('deleted_at')->where('id', $id)->first();
        abort_if(!$post, 404);

        $cat = $post->category_id ? DB::table('blogs_categories')->where('id', $post->category_id)->first() : null;
        return view('app.blog.show', compact('post', 'cat'));
    }

    public function create()
    {
        $categories = DB::table('blogs_categories')->orderBy('name')->get();
        return view('app.blog.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'slug'  => 'required|string|max:255',
        ]);

        DB::table('blogs_posts')->insert([
            'title'            => $request->input('title'),
            'sub_title'        => $request->input('sub_title'),
            'slug'             => $request->input('slug'),
            'content'          => $request->input('content', ''),
            'author_name'      => $request->input('author_name'),
            'is_published'     => $request->boolean('is_published'),
            'published_at'     => $request->boolean('is_published') ? now() : null,
            'category_id'      => $request->input('category_id') ?: null,
            'meta_title'       => $request->input('meta_title'),
            'meta_keywords'    => $request->input('meta_keywords'),
            'meta_description' => $request->input('meta_description'),
            'visits'           => 0,
            'created_at'       => now(),
            'updated_at'       => now(),
        ]);

        session()->flash('success', 'Blog post created.');
        return redirect()->route('blog.index');
    }

    public function togglePublish($id)
    {
        $post = DB::table('blogs_posts')->whereNull('deleted_at')->where('id', $id)->first();
        abort_if(!$post, 404);

        $newState = !$post->is_published;
        DB::table('blogs_posts')->where('id', $id)->update([
            'is_published' => $newState,
            'published_at' => $newState ? now() : null,
            'updated_at'   => now(),
        ]);

        session()->flash('success', $newState ? 'Post published.' : 'Post unpublished.');
        return back();
    }

    public function destroy($id)
    {
        DB::table('blogs_posts')->where('id', $id)->update(['deleted_at' => now()]);
        session()->flash('success', 'Post deleted.');
        return redirect()->route('blog.index');
    }
}

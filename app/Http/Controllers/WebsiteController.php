<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class WebsiteController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search', '');
        $filter = $request->input('filter', 'all');

        $query = DB::table('website_pages')->whereNull('deleted_at');

        if ($search) {
            $query->where('title', 'like', "%{$search}%");
        }

        if ($filter === 'published') {
            $query->where('is_published', 1);
        } elseif ($filter === 'draft') {
            $query->where('is_published', 0);
        }

        $pages = $query->orderByDesc('created_at')->paginate(20)->withQueryString();

        $total     = DB::table('website_pages')->whereNull('deleted_at')->count();
        $published = DB::table('website_pages')->whereNull('deleted_at')->where('is_published', 1)->count();
        $drafts    = $total - $published;

        return view('app.website.index', compact('pages', 'search', 'filter', 'total', 'published', 'drafts'));
    }

    public function show($id)
    {
        $page = DB::table('website_pages')->whereNull('deleted_at')->where('id', $id)->first();
        abort_if(!$page, 404);
        return view('app.website.show', compact('page'));
    }

    public function create()
    {
        return view('app.website.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'slug'  => 'required|string|max:255',
        ]);

        DB::table('website_pages')->insert([
            'title'            => $request->input('title'),
            'slug'             => $request->input('slug'),
            'content'          => $request->input('content', ''),
            'is_published'     => $request->boolean('is_published'),
            'is_header_visible'=> $request->boolean('is_header_visible', true),
            'is_footer_visible'=> $request->boolean('is_footer_visible', true),
            'meta_title'       => $request->input('meta_title'),
            'meta_keywords'    => $request->input('meta_keywords'),
            'meta_description' => $request->input('meta_description'),
            'published_at'     => $request->boolean('is_published') ? now() : null,
            'created_at'       => now(),
            'updated_at'       => now(),
        ]);

        session()->flash('success', 'Page created successfully.');
        return redirect()->route('website.index');
    }

    public function togglePublish($id)
    {
        $page = DB::table('website_pages')->whereNull('deleted_at')->where('id', $id)->first();
        abort_if(!$page, 404);

        $newState = !$page->is_published;
        DB::table('website_pages')->where('id', $id)->update([
            'is_published' => $newState,
            'published_at' => $newState ? now() : null,
            'updated_at'   => now(),
        ]);

        session()->flash('success', $newState ? 'Page published.' : 'Page unpublished.');
        return back();
    }

    public function destroy($id)
    {
        DB::table('website_pages')->where('id', $id)->update(['deleted_at' => now()]);
        session()->flash('success', 'Page deleted.');
        return redirect()->route('website.index');
    }
}

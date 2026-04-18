<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\NewsPost;
use App\Models\Category;
use App\Models\Area;

class NewsController extends Controller
{
    public function index(Request $request)
    {
        $query = NewsPost::with(['category', 'area', 'source'])
            ->where('publish_status', 'published')
            ->orderBy('published_at', 'desc');

        if ($request->filled('area')) {
            $query->where('area_id', $request->area);
        }

        if ($request->filled('category')) {
            $query->where('category_id', $request->category);
        }

        if ($request->filled('urgency')) {
            $query->where('urgency_level', $request->urgency);
        }

        $posts      = $query->paginate(12)->withQueryString();
        $areas      = Area::where('city_id', 1)->orderBy('name')->get();
        $categories = Category::orderBy('name')->get();

        return view('news.index', compact('posts', 'areas', 'categories'));
    }

    public function show($slug)
    {
        $news = NewsPost::where('slug', $slug)->orWhere('id', $slug)->firstOrFail();
        return view('news.show', ['post' => $news]);
    }
}

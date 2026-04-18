<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\NewsPost;
use App\Models\City;
use App\Models\Area;
use App\Models\Category;
use App\Models\Source;
use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class NewsPostController extends Controller
{
    use AuthorizesRequests;

    public function index(Request $request)
    {
        $this->authorize('viewAny', NewsPost::class);

        $query = NewsPost::with(['category', 'area', 'creator']);

        if ($request->status) {
            $query->where('publish_status', $request->status);
        }

        $posts = $query->latest()->paginate(15);

        return view('admin.news.index', compact('posts'));
    }

    public function create()
    {
        $this->authorize('create', NewsPost::class);

        $cities = City::all();
        $categories = Category::all();
        $sources = Source::all();
        $tags = Tag::all();

        $content_blocks = [
            'What Happened' => '',
            'Who is Affected' => '',
            'Duration' => '',
            'Official Source Statement' => '',
            'What Citizens Should Do' => '',
            'Contact Information' => ''
        ];

        return view('admin.news.form', compact(
            'cities',
            'categories',
            'sources',
            'tags',
            'content_blocks'
        ));
    }

    public function store(Request $request)
    {
        $this->authorize('create', NewsPost::class);

        // ✅ VALIDATION
        $validated = $request->validate([
            'title' => 'required',
            'summary' => 'required',
            'city_id' => 'required',
            'area_id' => 'required',
            'category_id' => 'required',
            'source_id' => 'required',
            'news_type' => 'required',
            'content_blocks' => 'required',

            'priority' => 'nullable|integer|min:0|max:10',
            'is_alert' => 'nullable|boolean',
        ]);

        // ✅ ASSIGN VALUES
        $validated['priority'] = $request->priority ?? 0;
        $validated['is_alert'] = $request->has('is_alert') ? 1 : 0;

        $validated['slug'] = Str::slug($request->title) . '-' . time();
        $validated['created_by'] = auth()->id();

        $post = NewsPost::create($validated);

        // ✅ STATE HANDLING
        if ($request->publish_status) {
            $this->authorize('update', $post);

            if ($request->publish_status === 'published') {
                $this->authorize('publish', $post);
            }

            $post->publish_status = $request->publish_status;
        }

        if ($request->verification_status) {
            $this->authorize('verify', $post);
            $post->verification_status = $request->verification_status;
        }

        if ($request->urgency_level) {
            if ($request->urgency_level === 'critical') {
                $this->authorize('markCritical', $post);
            }

            $post->urgency_level = $request->urgency_level;
        }

        $post->save();

        return redirect()
            ->route('admin.news.index')
            ->with('success', 'News post created.');
    }

    public function edit(NewsPost $news)
    {
        $this->authorize('view', $news);

        $cities = City::all();
        $areas = Area::all();
        $categories = Category::all();
        $sources = Source::all();
        $tags = Tag::all();

        return view('admin.news.form', [
            'post' => $news,
            'cities' => $cities,
            'areas' => $areas,
            'categories' => $categories,
            'sources' => $sources,
            'tags' => $tags,
            'content_blocks' => $news->content_blocks
        ]);
    }

    public function update(Request $request, NewsPost $news)
    {
        $this->authorize('update', $news);

        // ✅ VALIDATION
        $validated = $request->validate([
            'title' => 'required',
            'summary' => 'required',
            'city_id' => 'required',
            'area_id' => 'required',
            'category_id' => 'required',
            'source_id' => 'required',
            'news_type' => 'required',
            'content_blocks' => 'required',

            'priority' => 'nullable|integer|min:0|max:10',
            'is_alert' => 'nullable|boolean',
        ]);

        // ✅ ASSIGN VALUES
        $validated['priority'] = $request->priority ?? 0;
        $validated['is_alert'] = $request->has('is_alert') ? 1 : 0;

        $news->fill($validated);

        // ✅ STATE HANDLING
        if ($request->publish_status && $request->publish_status !== $news->publish_status) {
            if ($request->publish_status === 'published') {
                $this->authorize('publish', $news);
            }

            if ($request->publish_status === 'archived') {
                $this->authorize('archive', $news);
            }

            $news->publish_status = $request->publish_status;
        }

        if ($request->verification_status && $request->verification_status !== $news->verification_status) {
            $this->authorize('verify', $news);
            $news->verification_status = $request->verification_status;
        }

        if ($request->urgency_level && $request->urgency_level !== $news->urgency_level) {
            if ($request->urgency_level === 'critical') {
                $this->authorize('markCritical', $news);
            }

            $news->urgency_level = $request->urgency_level;
        }

        try {
            $news->save();
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }

        return redirect()
            ->route('admin.news.index')
            ->with('success', 'News post updated.');
    }

    public function destroy(NewsPost $news)
    {
        $this->authorize('delete', $news);

        $news->delete();

        return redirect()
            ->route('admin.news.index')
            ->with('success', 'News post deleted.');
    }
}

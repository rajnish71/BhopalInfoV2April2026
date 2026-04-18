<?php
namespace App\Http\Controllers;

use App\Models\NewsPost;
use Illuminate\Http\Response;

class FeedController extends Controller
{
    public function rss()
    {
        $posts = NewsPost::where('publish_status', 'published')
                         ->where('verification_status', 'verified')
                         ->latest('published_at')
                         ->limit(20)
                         ->get();

        return response()->view('feeds.rss', compact('posts'))
                         ->header('Content-Type', 'text/xml');
    }

    public function sitemap()
    {
        $posts = NewsPost::where('publish_status', 'published')
                         ->where('verification_status', 'verified')
                         ->get();

        return response()->view('feeds.sitemap', compact('posts'))
                         ->header('Content-Type', 'text/xml');
    }
}
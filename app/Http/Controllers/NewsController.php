<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\NewsPost;

class NewsController extends Controller
{
    public function show($slug)
    {
            $news = NewsPost::findOrFail($slug);
	    return view('news.show', ['post' => $news]);
    }
}

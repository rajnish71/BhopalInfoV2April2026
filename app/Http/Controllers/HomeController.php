<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\NewsPost;
use App\Models\Event;

class HomeController extends Controller
{
    public function index()
    {
        return view(theme_view('pages.home'));
    }
}

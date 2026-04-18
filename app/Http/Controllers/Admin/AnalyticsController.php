<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\NewsPost;
use App\Models\Area;
use App\Models\Category;
use Illuminate\Support\Facades\DB;

class AnalyticsController extends Controller {
    public function index() {
        $postsByPillar = Category::withCount('newsPosts')->get();
        $areaHeatmap = Area::withCount('newsPosts')->orderBy('news_posts_count', 'desc')->get();
        $criticalAlerts = NewsPost::where('urgency_level', 'critical')->count();
        $totalVerified = NewsPost::where('verification_status', 'verified')->count();
        
        return view('admin.analytics.index', compact('postsByPillar', 'areaHeatmap', 'criticalAlerts', 'totalVerified'));
    }
}
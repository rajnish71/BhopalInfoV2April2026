<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Category;
use App\Models\Media;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'users' => User::count(),
            'categories' => Category::count(),
            'media' => Media::count(),
            'audit_logs' => DB::table('audit_logs')->count(),
            'roles' => Role::count(),
        ];

        $latest_activity = DB::table('audit_logs')
            ->leftJoin('users', 'audit_logs.user_id', '=', 'users.id')
            ->select('audit_logs.*', 'users.name as user_name')
            ->orderBy('audit_logs.created_at', 'desc')
            ->limit(10)
            ->get();

        return view('admin.dashboard', compact('stats', 'latest_activity'));
    }
}
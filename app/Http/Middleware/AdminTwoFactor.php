<?php
namespace App\Http\Middleware;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
class AdminTwoFactor
{
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::user();
        if ($user && $user->hasRole('Super Admin')) {
            if (!$request->session()->has('2fa_verified')) {
                if (!$request->is('admin/2fa*')) {
                    return redirect()->route('admin.2fa.index');
                }
            }
        }
        return $next($request);
    }
}
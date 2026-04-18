<?php
namespace App\Http\Middleware;
use Closure;
use Illuminate\Http\Request;

class Admin2FAEnforcement
{
    public function handle(Request $request, Closure $next)
    {
        if (config('admin.require_admin_2fa') === true && auth()->check()) {
            if (auth()->user()->hasRole('Super Admin') && !$request->session()->get('2fa_verified')) {
                // Hook for future redirection to 2FA challenge
                // return redirect()->route('admin.2fa.index');
            }
        }
        return $next($request);
    }
}
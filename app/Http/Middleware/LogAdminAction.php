<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class LogAdminAction
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        if (Auth::check() && in_array($request->method(), ['POST', 'PUT', 'PATCH', 'DELETE'])) {
            $user = Auth::user();
            $action = $this->getActionName($request);
            $module = $this->getModuleName($request);
            $record_id = $this->getRecordId($request);

            DB::table('audit_logs')->insert([
                'user_id' => $user->id,
                'action' => $action,
                'module' => $module,
                'record_id' => $record_id,
                'ip_address' => $request->ip(),
                'created_at' => now(),
            ]);
        }

        return $response;
    }

    protected function getActionName(Request $request): string
    {
        return match ($request->method()) {
            'POST' => 'Create',
            'PUT', 'PATCH' => 'Update',
            'DELETE' => 'Delete',
            default => 'Unknown',
        };
    }

    protected function getModuleName(Request $request): string
    {
        $route = $request->route();
        return $route ? (explode('.', $route->getName())[0] ?? 'Global') : 'Global';
    }

    protected function getRecordId(Request $request): ?int
    {
        $route = $request->route();
        if ($route) {
            $params = $route->parameters();
            $firstParam = reset($params);
            return is_numeric($firstParam) ? (int)$firstParam : null;
        }
        return null;
    }
}
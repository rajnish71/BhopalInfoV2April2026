<?php
use App\Providers\AuthServiceProvider;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Support\Facades\Route;

return Application::configure(basePath: dirname(__DIR__))
    ->withProviders([
        AuthServiceProvider::class,
    ])
    ->withRouting(
        using: function () {
            Route::middleware('web')
                ->group(base_path('routes/web.php'));

            Route::middleware(['web', 'auth', 'verified', 'audit'])
                ->prefix('admin')
                ->group(base_path('routes/admin.php'));
        },
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->trustProxies(at: '*');
        $middleware->alias(['admin.2fa' => \App\Http\Middleware\Admin2FAEnforcement::class]);
        $middleware->alias([
            'role' => \Spatie\Permission\Middleware\RoleMiddleware::class,
            'permission' => \Spatie\Permission\Middleware\PermissionMiddleware::class,
            'role_or_permission' => \Spatie\Permission\Middleware\RoleOrPermissionMiddleware::class,
            'audit' => \App\Http\Middleware\LogAdminAction::class,
            '2fa' => \App\Http\Middleware\AdminTwoFactor::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })
    ->create();

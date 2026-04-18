<?php

namespace App\Providers;

use App\Models\Event;
use App\Policies\EventPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate; // ✅ CORRECT PLACE
use App\Models\NewsPost;
use App\Policies\NewsPostPolicy;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [
	    Event::class => EventPolicy::class,
	    NewsPost::class => NewsPostPolicy::class,
    ];

    public function boot(): void
    {
        $this->registerPolicies();

        // ✅ SUPER ADMIN OVERRIDE
        Gate::before(function ($user, $ability) {
            if ($user->hasRole('super-admin')) {
                return true;
            }
        });
    }
}

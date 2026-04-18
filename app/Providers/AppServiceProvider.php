<?php

namespace App\Providers;

use App\Models\Event as EventModel;
use App\Observers\EventObserver;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL;
use App\Events\NewsPublished;
use App\Listeners\SendNewsNotifications;
use App\Models\NewsPost;
use App\Observers\NewsPostObserver;
use App\Observers\UserObserver;
use App\Models\User;
use Illuminate\Support\Facades\Event;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        if (app()->environment('production')) {
            URL::forceScheme('https');
        }

        Event::listen(
            NewsPublished::class,
            SendNewsNotifications::class
        );

        NewsPost::observe(NewsPostObserver::class);
        User::observe(UserObserver::class);
        //Event Engine Observer
        EventModel::observe(EventObserver::class);
    }
}

<?php

namespace App\Providers;

use App\Models\Setting;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class SettingsServiceProvider extends ServiceProvider
{
    public function register(): void {}

    public function boot(): void
    {
        $settings = Cache::rememberForever('site_settings', function () {
            try {
                return Setting::all()->pluck('value', 'key');
            } catch (\Exception $e) {
                return collect();
            }
        });

        View::share('site', $settings);
    }
}
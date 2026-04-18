<?php

use Illuminate\Support\Facades\DB;

if (!function_exists('setting')) {
    function setting($key)
    {
        return DB::table('settings')->where('key', $key)->value('value');
    }
}

if (!function_exists('active_theme')) {
    function active_theme()
    {
        return cache()->remember('active_theme', 60, function () {
            return setting('active_theme') ?? 'modern2026';
        });
    }
}

if (!function_exists('theme_view')) {
    function theme_view($view)
    {
        return 'themes.' . active_theme() . '.' . $view;
    }
}

if (!function_exists('media_url')) {
    function media_url($path)
    {
        if (!$path) {
            return asset('images/hero.jpg');
        }

        return asset('storage/' . ltrim($path, '/'));
    }
}

if (!function_exists('theme_config')) {
    function theme_config()
    {
        $theme = active_theme();

        $path = resource_path("views/themes/{$theme}/config.php");

        return file_exists($path) ? include $path : [];
    }
}

if (!function_exists('theme_sections')) {
    function theme_sections($page)
    {
        $sections = \DB::table('theme_sections')
            ->where('page', $page)
            ->where('is_enabled', 1)
            ->orderBy('position')
            ->pluck('section')
            ->toArray();

        // fallback to config if DB empty
        if (empty($sections)) {
            $config = theme_config();
            return $config[$page]['sections'] ?? [];
        }

        return $sections;
    }
}

if (!function_exists('section_setting')) {
    function section_setting($section, $key, $default = null)
    {
        return \DB::table('section_settings')
            ->where('section', $section)
            ->where('setting_key', $key)
            ->value('setting_value') ?? $default;
    }
}

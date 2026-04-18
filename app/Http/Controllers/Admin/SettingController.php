<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class SettingController extends Controller
{
    public function editTheme()
    {
        $themePath = resource_path('views/themes');

        $themes = collect(scandir($themePath))
            ->filter(fn($item) => $item !== '.' && $item !== '..' && is_dir($themePath.'/'.$item))
            ->map(function ($theme) use ($themePath) {

                $jsonPath = $themePath . '/' . $theme . '/theme.json';

                if (file_exists($jsonPath)) {
                    $data = json_decode(file_get_contents($jsonPath), true);

                    return [
                        'key' => $data['key'] ?? $theme,
                        'name' => $data['name'] ?? $theme,
                        'description' => $data['description'] ?? '',
                        'preview' => $data['preview'] ?? null,
                        'version' => $data['version'] ?? '1.0',
                    ];
                }

                return [
                    'key' => $theme,
                    'name' => $theme,
                    'description' => '',
                    'preview' => null,
                    'version' => '1.0',
                ];
            })
            ->values();

        $activeTheme = DB::table('settings')
            ->where('key', 'active_theme')
            ->value('value');
$categories = DB::table('categories')->pluck('name', 'id');
        return view('admin.settings.theme', compact('themes', 'activeTheme', 'categories'));
    }

    public function updateTheme(Request $request)
    {
        DB::table('settings')->updateOrInsert(
            ['key' => 'active_theme'],
            ['value' => $request->theme]
        );

        cache()->forget('active_theme');

        return redirect()->back()->with('success', 'Theme updated successfully');
    }

    public function updateSections(Request $request)
    {
        foreach ($request->sections as $index => $section) {

            DB::table('theme_sections')
                ->where('section', $section['name'])
                ->where('page', 'home')
                ->update([
                    'position' => $index + 1,
                    'is_enabled' => $section['enabled'] ?? 0
                ]);
        }

        return response()->json(['success' => true]);
    }

public function updateSectionSettings(Request $request)
{
    foreach ($request->settings as $section => $values) {

        foreach ($values as $key => $value) {

            DB::table('section_settings')->updateOrInsert(
                [
                    'section' => $section,
                    'setting_key' => $key
                ],
                [
                    'setting_value' => $value
                ]
            );
        }
    }

    return redirect()->back()->with('success', 'Settings updated');
}

}

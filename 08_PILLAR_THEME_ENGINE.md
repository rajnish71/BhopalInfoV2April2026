# PILLAR 8 — THEME ENGINE & THEME MANAGEMENT CMS
**Version:** 1.0 | **Created:** April 2026
**Scope:** Theme architecture, dynamic theme loading, Theme Management Admin, long-term CMS vision.
**Do not:** Redesign business logic engines — themes are presentation only.
**Depends on:** Anchor Doc (brand rules, platform_settings table, RBAC roles)

---

## CURRENT STATE (April 2026)

| Component | Status | Location |
|---|---|---|
| Basic theme folder | EXISTS | `resources/views/themes/modern2026/` |
| Theme switching logic | Unknown — needs review | TBD |
| themes DB table | Unknown — needs creation | TBD |
| Theme management admin | Does not exist yet | To build |
| Component standardization | Partial / unknown | Needs audit |

---

## ARCHITECTURAL REVIEW — WHAT YOU HAVE VS WHAT YOU NEED

### What you described (current attempt)

```
resources/views/themes/
    modern2026/
        layout.blade.php
        home.blade.php
        news/show.blade.php
        components/
            header.blade.php
            footer.blade.php
```

This is a good starting direction. The folder structure is correct. However, a basic folder structure alone has several gaps that will cause pain as the project scales. This review covers what needs to change and why.

---

## ARCHITECTURAL PROBLEMS TO FIX NOW

### Problem 1 — No standardized theme contract
Currently there is no enforced rule about what files every theme MUST contain. If `modern2026` has `news/show.blade.php` but a future theme doesn't, the fallback will break or silently fail. Every theme must fulfill a defined contract — a required file list.

**Fix:** Define a `THEME_CONTRACT` — a list of required view files every theme must implement. Enforce this at theme activation time.

### Problem 2 — Theme loading is likely scattered
If theme loading is done inline in controllers like `return view('themes.modern2026.home')`, then when you change the theme name you have to hunt through every controller. This is not scalable.

**Fix:** Centralize all theme view resolution through a single `ThemeService` class and a global `theme()` helper. Controllers never reference theme names directly.

### Problem 3 — No database registry yet
Without a `themes` table, the "active theme" is probably hardcoded somewhere. This makes admin-controlled switching impossible.

**Fix:** Create `themes` table and `platform_settings` entry for `active_theme`. All theme switching flows through this.

### Problem 4 — No asset pipeline per theme
CSS and JS for `modern2026` are likely in the global `public/` directory. When you add a second theme, asset conflicts will occur.

**Fix:** Each theme gets its own asset subfolder: `public/themes/modern2026/` with its own compiled CSS/JS. The theme loader also switches the asset path.

### Problem 5 — No theme configuration layer
Themes need settings — color overrides, font choices, section visibility toggles. Without a config layer, every visual change requires code edits.

**Fix:** Each theme has a `config.json` inside its folder. The Theme Management CMS reads and writes to this file (or a DB equivalent).

---

## CORRECT LONG-TERM ARCHITECTURE

### Layer 1 — Theme Folder Structure (enforced contract)

```
resources/views/themes/
    modern2026/
        config.json             ← Theme metadata + settings
        layout.blade.php        ← Master layout (required)
        home.blade.php          ← Homepage (required)
        pages/
            about.blade.php
        news/
            index.blade.php     ← Required
            show.blade.php      ← Required
        events/
            index.blade.php
            show.blade.php
        business/
            index.blade.php
            show.blade.php
        components/
            header.blade.php    ← Required
            footer.blade.php    ← Required
            hero.blade.php
            news-card.blade.php
            event-card.blade.php
            alert-strip.blade.php
            breadcrumb.blade.php
        errors/
            404.blade.php
            500.blade.php

public/themes/
    modern2026/
        css/
            app.css
        js/
            app.js
        images/
```

### Theme contract (required files — enforced at activation)

```php
const THEME_CONTRACT = [
    'layout.blade.php',
    'home.blade.php',
    'news/index.blade.php',
    'news/show.blade.php',
    'components/header.blade.php',
    'components/footer.blade.php',
    'errors/404.blade.php',
];
```

If any required file is missing from a theme → activation blocked with clear error message.

### `config.json` per theme

```json
{
    "name": "Modern 2026",
    "version": "1.0.0",
    "author": "Bhopal.info",
    "description": "Clean civic theme for 2026 platform launch",
    "supports": ["news", "events", "business", "contests"],
    "settings": {
        "show_hero": true,
        "show_alert_strip": true,
        "show_business_spotlight": true,
        "show_newsletter_block": true,
        "cards_per_row": 3
    }
}
```

---

## DATABASE TABLES (THEME ENGINE)

### `themes`

| Field | Type | Notes |
|---|---|---|
| id | BIGINT PK | |
| name | VARCHAR(100) | Display name: "Modern 2026" |
| folder | VARCHAR(100) | Folder name: "modern2026" |
| version | VARCHAR(20) | "1.0.0" |
| description | TEXT | Nullable |
| screenshot | VARCHAR | Path to preview image — nullable |
| is_active | BOOLEAN | Only one can be true at a time |
| is_installed | BOOLEAN | Theme files exist on disk |
| settings_json | JSON | Theme-specific settings (from config.json) |
| created_at | TIMESTAMP | |
| updated_at | TIMESTAMP | |

**Rule:** When activating a theme, all other themes set `is_active = false` in the same transaction. This also updates `platform_settings` key `active_theme`.

### `theme_settings` (optional — for per-entity overrides, Phase 2)

| Field | Type | Notes |
|---|---|---|
| id | BIGINT PK | |
| theme_id | FK → themes | |
| city_id | FK → cities | Nullable — city-specific override |
| key | VARCHAR | Setting key |
| value | TEXT | Setting value |

This enables: Bhopal uses modern2026 with blue tones, Indore uses same theme with red tones.

---

## MIGRATION ORDER

1. create_themes_table
2. create_theme_settings_table (Phase 2 — multi-city)
3. Seed: insert modern2026 as default active theme
4. Add `active_theme` to platform_settings seeds

---

## THEME SERVICE (CORE ENGINE)

**Class:** `App\Services\ThemeService`

```php
class ThemeService
{
    // Get active theme folder name
    public function active(): string
    {
        return setting('active_theme', 'modern2026');
    }

    // Resolve a view path for the active theme
    public function view(string $view): string
    {
        $theme = $this->active();
        $themeView = "themes.{$theme}.{$view}";

        // Fallback: if view doesn't exist in theme, use default
        if (view()->exists($themeView)) {
            return $themeView;
        }

        // Log missing view for developer awareness
        logger()->warning("Theme view missing: {$themeView}, falling back to default");
        return "themes.default.{$view}";
    }

    // Resolve asset path for active theme
    public function asset(string $path): string
    {
        $theme = $this->active();
        return asset("themes/{$theme}/{$path}");
    }

    // Validate a theme fulfills the contract before activation
    public function validate(string $folder): array
    {
        $missing = [];
        foreach (self::THEME_CONTRACT as $file) {
            $viewPath = "themes.{$folder}." . str_replace(['/', '.blade.php'], ['.', ''], $file);
            if (!view()->exists($viewPath)) {
                $missing[] = $file;
            }
        }
        return $missing; // Empty = valid
    }

    // Activate a theme
    public function activate(string $folder): bool
    {
        $missing = $this->validate($folder);
        if (!empty($missing)) {
            throw new \Exception("Theme missing required files: " . implode(', ', $missing));
        }

        DB::transaction(function() use ($folder) {
            Theme::query()->update(['is_active' => false]);
            Theme::where('folder', $folder)->update(['is_active' => true]);
            PlatformSetting::where('key', 'active_theme')->update(['value' => $folder]);
            cache()->forget('setting_active_theme');
        });

        return true;
    }
}
```

**Global helper:**
```php
// In helpers.php
function theme(string $view): string
{
    return app(ThemeService::class)->view($view);
}

function theme_asset(string $path): string
{
    return app(ThemeService::class)->asset($path);
}
```

**In controllers — clean and theme-agnostic:**
```php
// WRONG — hardcoded, not scalable
return view('themes.modern2026.home', $data);

// CORRECT — theme-agnostic
return view(theme('home'), $data);
```

**In Blade files:**
```blade
<link rel="stylesheet" href="{{ theme_asset('css/app.css') }}">
@include(theme('components.header'))
```

---

## FALLBACK SYSTEM

Three-level fallback:

```
1. Check: themes/{active_theme}/{view} → use if exists
2. Check: themes/default/{view} → use if active theme missing that view
3. Abort with 404 if neither exists (should never happen if contract enforced)
```

**`themes/default/`** — a minimal skeleton theme that ships with the platform. Every required contract file exists here, outputting minimal unstyled HTML. Acts as safety net only.

---

## THEME MANAGEMENT ADMIN (CMS)

**Access:** Super Admin only via platform_settings RBAC

### Dashboard screen
```
Installed Themes
┌─────────────────────────────────────────────┐
│  [Screenshot]  Modern 2026          ACTIVE  │
│                Version 1.0.0                │
│                                             │
│  [Settings]  [Preview]  [Deactivate]        │
└─────────────────────────────────────────────┘

┌─────────────────────────────────────────────┐
│  [Screenshot]  Classic Civic         —      │
│                Version 1.0.0                │
│                                             │
│  [Settings]  [Preview]  [Activate]          │
└─────────────────────────────────────────────┘
```

### Theme settings screen
Reads `config.json` from theme folder. Displays toggles and inputs for each setting key. Saves to `themes.settings_json` column. No code editing required.

```
Theme: Modern 2026 — Settings

Homepage Hero           [ON / OFF]
Alert Strip             [ON / OFF]
Business Spotlight      [ON / OFF]
Newsletter Block        [ON / OFF]
Cards per row           [2] [3] [4]

Primary color override  [___________] (future — Phase 2)

[Save Settings]
```

### Theme activation flow
```
Admin clicks "Activate"
→ ThemeService::validate() checks contract
→ If missing files → show error list, block activation
→ If valid → ThemeService::activate() runs
→ DB transaction: deactivate all, activate selected
→ Cache cleared
→ Redirect with success message
```

### Preview mode (Phase 2)
```
Admin clicks "Preview"
→ Session variable: preview_theme = 'classic_civic'
→ ThemeService::active() checks session first
→ Admin sees preview without affecting live site
→ "Exit Preview" clears session variable
```

---

## ISOLATION RULES (HARD)

Themes must contain ONLY:
- Blade templates
- CSS / JS / images (in public/themes/)
- Layout structure
- Component includes

Themes must NEVER contain:
- DB queries
- Business logic
- Auth checks
- Controller logic
- Config reads (except via passed `$settings` variable)

**Data flow:** Controller → passes `$data` to view → theme renders `$data`. The theme never fetches its own data.

---

## MULTI-CITY POWER

Because `cities` table exists and every entity has `city_id`:

Phase 1: All cities use same active theme.

Phase 2 (via `theme_settings` table):
```
Bhopal city_id=1 → active theme: modern2026 (default settings)
Indore city_id=2 → active theme: modern2026 (different color settings)
```

Phase 3 (future):
```
Different theme per city entirely
Indore → modern2027 theme
Bhopal → modern2026 theme
```

No rebuild required — architecture supports this from Day 1.

---

## ASSET COMPILATION STRATEGY

Each theme has its own compiled assets. Do NOT use a shared global CSS that themes override — that creates specificity wars and makes themes fragile.

**Recommended per-theme build setup:**

```
resources/
    themes/
        modern2026/
            sass/
                app.scss    ← imports brand variables
            js/
                app.js

public/
    themes/
        modern2026/
            css/app.css     ← compiled output
            js/app.js       ← compiled output
```

Brand color variables defined once, imported per theme:

```scss
// resources/themes/modern2026/sass/_variables.scss
$primary-red: #B71C1C;
$accent-green: #8FB339;
$primary-orange: #F57C00;
$background: #FFFFFF;
$black: #000000;

// NO gradients
// NO shadows
// Flat design only
```

---

## DEVELOPMENT PHASE PLAN

### Phase A (Now — do these immediately)
- [ ] Audit existing `modern2026` folder — list what files currently exist
- [ ] Define THEME_CONTRACT (required file list)
- [ ] Create `ThemeService` class
- [ ] Create `theme()` and `theme_asset()` global helpers
- [ ] Refactor all controller `view()` calls to use `theme()` helper
- [ ] Create `themes` DB table + migration
- [ ] Seed modern2026 as active theme
- [ ] Add `active_theme` to platform_settings
- [ ] Create `themes/default/` skeleton as fallback

### Phase B (After Phase A is stable)
- [ ] Build Theme Management Admin screen (list + activate)
- [ ] Add theme settings screen (reads/writes config.json)
- [ ] Add screenshot upload for themes
- [ ] Create second theme to validate the architecture works

### Phase C (Multi-city preparation)
- [ ] Create `theme_settings` table
- [ ] Per-city theme overrides
- [ ] Preview mode (session-based)
- [ ] Theme packaging / upload system

---

## WHAT THE THEME CMS WILL EVENTUALLY LOOK LIKE

Long-term vision for the Theme Management section in Super Admin:

```
Admin → Appearance → Themes
  ├── Installed Themes (list with screenshots)
  ├── Active Theme Settings (visual toggles)
  ├── Upload New Theme
  ├── Preview Mode
  └── Per-City Overrides (Phase C)

Admin → Appearance → Customize (future)
  ├── Colors (primary, accent — within brand rules)
  ├── Typography (font scale)
  ├── Layout (sidebar on/off, grid columns)
  ├── Sections (show/hide homepage blocks)
  └── Custom CSS (Director only)
```

This is NOT WordPress's customizer. It is a controlled civic UI management panel. Only settings that are safe to change are exposed. Brand rules (red, green, no gradients) are enforced at the framework level — not overridable by admins.

---

## OPEN QUESTIONS FOR THIS PILLAR

- [ ] Audit modern2026 folder — what files currently exist? List them.
- [ ] Is there currently a ThemeService or is theme loading scattered in controllers?
- [ ] Is `active_theme` already in platform_settings or hardcoded?
- [ ] Is there a separate Vite/Mix config per theme, or one global build?
- [ ] Should theme screenshots be stored in `public/themes/` or in `storage/`?
- [ ] Preview mode priority — is this needed before Phase B launch?
- [ ] Should theme config be stored in `config.json` file or entirely in `themes.settings_json` DB column?

<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class BhopalGeoSeeder extends Seeder
{
    public function run(): void
     { 
	     $this->call(BhopalGeoSeeder::class);
        // ── CITIES ──────────────────────────────────────────────
        // Force id=1 so all existing events (city_id=1) remain valid
        DB::table('cities')->insertOrIgnore([
            'id'         => 1,
            'name'       => 'Bhopal',
            'slug'       => 'bhopal',
            'is_active'  => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // ── AREAS ────────────────────────────────────────────────
        // id=1 must be first — all existing events use area_id=1
        // Remaining areas get natural auto-increment ids
        $areas = [
            // id=1 — matches existing event data
            ['id' => 1,    'name' => 'MP Nagar',          'slug' => 'mp-nagar'],

            // Core areas — civic priority order
            ['id' => null, 'name' => 'Arera Colony',       'slug' => 'arera-colony'],
            ['id' => null, 'name' => 'Kolar',              'slug' => 'kolar'],
            ['id' => null, 'name' => 'New Market',         'slug' => 'new-market'],
            ['id' => null, 'name' => 'TT Nagar',           'slug' => 'tt-nagar'],
            ['id' => null, 'name' => 'Habibganj',          'slug' => 'habibganj'],
            ['id' => null, 'name' => 'Bhopal Old City',    'slug' => 'bhopal-old-city'],
            ['id' => null, 'name' => 'Shahpura',           'slug' => 'shahpura'],
            ['id' => null, 'name' => 'Ayodhya Bypass',     'slug' => 'ayodhya-bypass'],
            ['id' => null, 'name' => 'Bairagarh',          'slug' => 'bairagarh'],
            ['id' => null, 'name' => 'Govindpura',         'slug' => 'govindpura'],
            ['id' => null, 'name' => 'Misrod',             'slug' => 'misrod'],
            ['id' => null, 'name' => 'Bagmugalia',         'slug' => 'bagmugalia'],
            ['id' => null, 'name' => 'Katara Hills',       'slug' => 'katara-hills'],
            ['id' => null, 'name' => 'Trilanga',           'slug' => 'trilanga'],
            ['id' => null, 'name' => 'Hoshangabad Road',   'slug' => 'hoshangabad-road'],
            ['id' => null, 'name' => 'Berasia Road',       'slug' => 'berasia-road'],
            ['id' => null, 'name' => 'Chunabhatti',        'slug' => 'chunabhatti'],
            ['id' => null, 'name' => 'Karond',             'slug' => 'karond'],
            ['id' => null, 'name' => 'Piplani',            'slug' => 'piplani'],
        ];

        foreach ($areas as $area) {
            $row = [
                'city_id'    => 1,
                'name'       => $area['name'],
                'slug'       => $area['slug'],
                'is_active'  => true,
                'created_at' => now(),
                'updated_at' => now(),
            ];

            if ($area['id'] !== null) {
                // Insert with explicit id to match existing FK references
                $row['id'] = $area['id'];
                DB::table('areas')->insertOrIgnore($row);
            } else {
                DB::table('areas')->insertOrIgnore($row);
            }
        }

        $this->command->info('✔ Cities seeded: Bhopal (id=1)');
        $this->command->info('✔ Areas seeded: 20 Bhopal areas (MP Nagar as id=1)');
        $this->command->info('✔ All existing events (city_id=1, area_id=1) remain valid');
    }
}

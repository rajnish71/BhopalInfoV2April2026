<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class EventCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            'Cultural',
            'Exhibition',
            'Sports',
            'Workshop',
            'Public Program',
        ];

        foreach ($categories as $category) {
            DB::table('event_categories')->updateOrInsert(
                ['slug' => Str::slug($category)],
                [
                    'name' => $category,
                    'is_active' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }
    }
}

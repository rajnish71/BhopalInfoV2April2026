<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\City;

class CitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        City::updateOrCreate(
            ['slug' => 'bhopal'],
            [
                'name' => 'Bhopal',
                'is_active' => true,
            ]
        );
    }
}

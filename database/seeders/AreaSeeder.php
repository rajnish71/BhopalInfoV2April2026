<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Area;
use App\Models\City;

class AreaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $city = City::where('slug', 'bhopal')->first();

        if ($city) {
            Area::updateOrCreate(
                ['slug' => 'mp-nagar', 'city_id' => $city->id],
                [
                    'name' => 'MP Nagar',
                ]
            );
        }
    }
}

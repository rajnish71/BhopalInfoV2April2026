<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\EventOrganizer;

class EventOrganizerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        EventOrganizer::updateOrCreate(
            ['email' => 'events@bhopal.info'],
            [
                'name' => 'Bhopal Events Team',
                'phone' => '0000000000',
                'trust_level' => 'trusted',
            ]
        );
    }
}

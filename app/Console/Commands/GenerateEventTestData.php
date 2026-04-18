<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Event;
use App\Models\EventTicketCategory;

class GenerateEventTestData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'events:generate-test-data';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate 10 events with 2 ticket categories each using factories';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Generating test data for events...');

        Event::factory()
            ->count(10)
            ->create()
            ->each(function ($event) {
                EventTicketCategory::factory()
                    ->count(2)
                    ->create([
                        'event_id' => $event->id,
                    ]);
            });

        $this->info('Successfully generated 10 events with 2 ticket categories each.');
    }
}

<?php

namespace Database\Factories;

use App\Models\EventTicketCategory;
use App\Models\Event;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\EventTicketCategory>
 */
class EventTicketCategoryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'event_id' => Event::first()?->id ?? Event::factory(),
            'name' => $this->faker->word . ' Ticket',
            'price' => $this->faker->randomFloat(2, 0, 1000),
            'quantity_total' => 100,
            'quantity_sold' => 0,
            'is_active' => true,
        ];
    }
}

<?php

namespace Database\Factories;

use App\Models\Event;
use App\Models\City;
use App\Models\Area;
use App\Models\EventCategory;
use App\Models\EventOrganizer;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Event>
 */
class EventFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $title = $this->faker->sentence;
        return [
            'city_id' => City::first()?->id ?? City::factory(),
            'area_id' => Area::first()?->id ?? Area::factory(),
            'category_id' => EventCategory::first()?->id ?? EventCategory::factory(),
            'organizer_id' => EventOrganizer::first()?->id ?? EventOrganizer::factory(),
            'created_by' => User::first()?->id ?? User::factory(),
            'title' => $title,
            'slug' => Str::slug($title),
            'summary' => $this->faker->paragraph,
            'description' => $this->faker->text,
            'venue' => $this->faker->company,
            'address' => $this->faker->address,
            'event_type' => $this->faker->randomElement(['free', 'paid']),
            'start_datetime' => $this->faker->dateTimeBetween('+1 week', '+1 month'),
            'end_datetime' => $this->faker->dateTimeBetween('+1 month', '+2 months'),
            'publish_status' => 'draft',
            'verification_status' => 'pending',
        ];
    }
}

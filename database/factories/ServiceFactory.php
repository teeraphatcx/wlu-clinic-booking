<?php

namespace Database\Factories;

use App\Models\Service;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Service>
 */
class ServiceFactory extends Factory
{
    protected $model = Service::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'code' => 'SRV-'.fake()->unique()->numerify('###'),
            'name_th' => 'บริการ'.fake('th_TH')->words(3, true),
            'name_en' => fake()->words(3, true),
            'description' => fake('th_TH')->sentence(),
            'price' => fake()->randomElement([200, 300, 450, 500]),
            'duration_minutes' => fake()->randomElement([30, 45, 60, 90]),
            'requires_therapist_selection' => true,
            'image' => null,
            'sort_order' => fake()->numberBetween(1, 20),
            'is_active' => true,
        ];
    }
}

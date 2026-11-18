<?php

namespace Database\Factories;

use App\Models\Therapist;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Therapist>
 */
class TherapistFactory extends Factory
{
    protected $model = Therapist::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'code' => 'TH-'.fake()->unique()->numerify('###'),
            'first_name' => fake('th_TH')->firstName(),
            'last_name' => fake('th_TH')->lastName(),
            'nickname' => 'หมอ'.fake('th_TH')->firstName(),
            'phone' => fake()->numerify('08########'),
            'gender' => fake()->randomElement(['male', 'female', 'other']),
            'photo' => null,
            'bio' => fake('th_TH')->sentence(),
            'start_date' => fake()->dateTimeBetween('-5 years', 'now')->format('Y-m-d'),
            'is_active' => true,
        ];
    }
}

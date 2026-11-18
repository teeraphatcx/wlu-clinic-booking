<?php

namespace Database\Factories;

use App\Models\Customer;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Customer>
 */
class CustomerFactory extends Factory
{
    protected $model = Customer::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'hn' => 'HN-'.fake()->unique()->numerify('#####'),
            'first_name' => fake('th_TH')->firstName(),
            'last_name' => fake('th_TH')->lastName(),
            'phone' => fake()->numerify('08########'),
            'email' => fake()->unique()->safeEmail(),
            'gender' => fake()->randomElement(['male', 'female', 'other']),
            'birthdate' => fake()->dateTimeBetween('-60 years', '-18 years')->format('Y-m-d'),
            'address' => fake('th_TH')->address(),
            'pdpa_accepted_at' => now(),
            'is_active' => true,
            'notes' => fake()->sentence(),
        ];
    }
}

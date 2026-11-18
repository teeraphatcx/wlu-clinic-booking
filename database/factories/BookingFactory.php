<?php

namespace Database\Factories;

use App\Models\Booking;
use App\Models\Customer;
use App\Models\Service;
use App\Models\TimeSlot;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Booking>
 */
class BookingFactory extends Factory
{
    protected $model = Booking::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $date = fake()->dateTimeBetween('now', '+14 days')->format('Y-m-d');

        return [
            'booking_no' => Booking::generateBookingNo($date).'-'.fake()->unique()->numerify('####'),
            'customer_id' => Customer::factory(),
            'service_id' => Service::factory(),
            'therapist_id' => null,
            'clinic_schedule_id' => null,
            'time_slot_id' => TimeSlot::first() ?? 1,
            'booking_date' => $date,
            'status' => fake()->randomElement(['pending', 'confirmed', 'completed']),
            'notes' => fake()->optional()->sentence(),
            'admin_notes' => null,
            'pdpa_accepted' => true,
            'ip_address' => '127.0.0.1',
        ];
    }
}

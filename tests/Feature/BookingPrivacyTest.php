<?php

namespace Tests\Feature;

use App\Models\Booking;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BookingPrivacyTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_booking_number_alone_cannot_expose_booking_details_or_confirmation(): void
    {
        $booking = Booking::with('customer')->firstOrFail();

        $this->get(route('booking.show', $booking->booking_no))->assertNotFound();
        $this->get(route('booking.confirm', $booking->booking_no))->assertNotFound();
        $this->get(route('booking.reschedule-form', $booking->booking_no))->assertNotFound();
        $this->post(route('booking.cancel', $booking->booking_no), ['reason' => 'ทดสอบ'])->assertNotFound();
    }
}

<?php

namespace App\Listeners;

use App\Contracts\NotificationDriver;
use App\Events\BookingCreated;
use Illuminate\Support\Facades\Log;

class SendBookingConfirmation
{
    public function __construct(private NotificationDriver $notifications) {}

    /**
     * Handle the event.
     */
    public function handle(BookingCreated $event): void
    {
        try {
            $this->notifications->sendBookingConfirmation($event->booking);
        } catch (\Throwable $exception) {
            Log::error('Booking notification failed', ['booking' => $event->booking->booking_no, 'error' => $exception->getMessage()]);
        }
    }
}

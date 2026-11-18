<?php

namespace App\Listeners;

use App\Contracts\NotificationDriver;
use App\Events\BookingCancelled;
use Illuminate\Support\Facades\Log;

class SendCancellationNotification
{
    public function __construct(private NotificationDriver $notifications) {}

    /**
     * Handle the event.
     */
    public function handle(BookingCancelled $event): void
    {
        try {
            $this->notifications->sendCancellationNotification($event->booking);
        } catch (\Throwable $exception) {
            Log::error('Cancellation notification failed', ['booking' => $event->booking->booking_no, 'error' => $exception->getMessage()]);
        }
    }
}

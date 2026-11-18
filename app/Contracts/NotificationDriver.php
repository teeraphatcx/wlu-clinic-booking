<?php

namespace App\Contracts;

use App\Models\Booking;

interface NotificationDriver
{
    /**
     * Send booking confirmation notification.
     */
    public function sendBookingConfirmation(Booking $booking): bool;

    /**
     * Send booking cancellation notification.
     */
    public function sendCancellationNotification(Booking $booking): bool;

    /**
     * Send appointment reminder notification.
     */
    public function sendReminder(Booking $booking): bool;
}

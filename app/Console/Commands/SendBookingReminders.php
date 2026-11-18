<?php

namespace App\Console\Commands;

use App\Contracts\NotificationDriver;
use App\Models\Booking;
use App\Models\Setting;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class SendBookingReminders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bookings:send-reminders';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'ส่งการแจ้งเตือนเตือนความจำล่วงหน้าสำหรับการจองคิว';

    /**
     * Execute the console command.
     */
    public function handle(NotificationDriver $notifications): int
    {
        if (! Setting::get('notify_booking_reminder', false)) {
            $this->info('ปิดการแจ้งเตือนเตือนนัดหมายอยู่');

            return self::SUCCESS;
        }
        $reminderBeforeHours = (int) Setting::get('reminder_before_hours', 24);
        $now = Carbon::now();
        $targetWindowEnd = $now->copy()->addHours($reminderBeforeHours);

        $this->info("Scanning bookings for reminders within the next {$reminderBeforeHours} hours...");

        $bookings = Booking::with(['customer', 'service', 'timeSlot', 'therapist'])
            ->where('status', Booking::STATUS_CONFIRMED)
            ->whereNull('reminder_sent_at')
            ->whereBetween('booking_date', [$now->toDateString(), $targetWindowEnd->toDateString()])
            ->get();

        $count = 0;
        foreach ($bookings as $booking) {
            $slotStartTime = $booking->timeSlot ? $booking->timeSlot->start_time : '08:00:00';
            $appointmentTime = Carbon::parse($booking->booking_date->format('Y-m-d').' '.$slotStartTime);

            if ($appointmentTime->isAfter($now) && $appointmentTime->lessThanOrEqualTo($targetWindowEnd)) {
                try {
                    if ($notifications->sendReminder($booking)) {
                        $booking->reminder_sent_at = Carbon::now();
                        $booking->save();
                        Log::info("Sent reminder for booking #{$booking->booking_no}");
                        $count++;
                    }
                } catch (\Throwable $exception) {
                    Log::error('Reminder notification failed', ['booking' => $booking->booking_no, 'error' => $exception->getMessage()]);
                }
            }
        }

        $this->info("Successfully sent {$count} reminder(s).");

        return self::SUCCESS;
    }
}

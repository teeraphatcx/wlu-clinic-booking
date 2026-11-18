<?php

namespace App\Services\Notifications;

use App\Contracts\NotificationDriver;
use App\Models\Booking;
use App\Models\Setting;
use Illuminate\Support\Facades\Mail;

class EmailNotificationDriver implements NotificationDriver
{
    public function sendBookingConfirmation(Booking $booking): bool
    {
        return $this->send($booking, 'notify_booking_created', 'ยืนยันการจองคิว', 'การจองของท่านได้รับการบันทึกแล้ว');
    }

    public function sendCancellationNotification(Booking $booking): bool
    {
        return $this->send($booking, 'notify_booking_cancelled', 'ยกเลิกการจองคิว', 'รายการจองของท่านถูกยกเลิกแล้ว');
    }

    public function sendReminder(Booking $booking): bool
    {
        return $this->send($booking, 'notify_booking_reminder', 'แจ้งเตือนนัดหมายคลินิกแพทย์แผนไทย', 'ท่านมีนัดหมายรับบริการที่คลินิก');
    }

    private function send(Booking $booking, string $setting, string $subject, string $intro): bool
    {
        $email = $booking->customer?->email;
        $channels = Setting::get('notification_channels', ['email']);
        if (! Setting::get($setting, false) || ! $email || ! in_array('email', is_array($channels) ? $channels : [], true)) {
            return false;
        }

        $booking->loadMissing(['service', 'therapist', 'timeSlot', 'customer']);
        $details = "{$intro}\n\nรหัสการจอง: {$booking->booking_no}\nหัตถการ: {$booking->service?->name_th}\nวันที่: {$booking->booking_date?->format('d/m/Y')}\nเวลา: ".substr($booking->timeSlot?->start_time ?? '', 0, 5)."\nหมอนวด: ".($booking->therapist?->displayName ?? 'ระบบจัดให้')."\n\nโปรดเก็บรหัสการจองไว้สำหรับตรวจสอบข้อมูล";
        Mail::raw($details, fn ($message) => $message->to($email)->subject($subject));

        return true;
    }
}

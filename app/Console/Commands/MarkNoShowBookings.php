<?php

namespace App\Console\Commands;

use App\Models\Booking;
use App\Models\BookingHistory;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class MarkNoShowBookings extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bookings:mark-no-show';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'เปลี่ยนสถานะเป็น no_show สำหรับการจองที่ผ่านไปแล้วโดยไม่ได้รับการบันทึก completed';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $now = Carbon::now();
        $todayStr = $now->toDateString();
        $currentTimeStr = $now->toTimeString();

        $this->info('Checking for past uncompleted bookings to mark as no_show...');

        // 1. Bookings from past dates that are still confirmed or pending
        $pastBookings = Booking::with('timeSlot')
            ->whereIn('status', [Booking::STATUS_CONFIRMED, Booking::STATUS_PENDING])
            ->where(function ($query) use ($todayStr, $currentTimeStr) {
                $query->where('booking_date', '<', $todayStr)
                    ->orWhere(function ($q) use ($todayStr, $currentTimeStr) {
                        $q->where('booking_date', '=', $todayStr)
                            ->whereHas('timeSlot', function ($slotQuery) use ($currentTimeStr) {
                                $slotQuery->where('end_time', '<=', $currentTimeStr);
                            });
                    });
            })
            ->get();

        $count = 0;
        foreach ($pastBookings as $booking) {
            $oldStatus = $booking->status;
            $booking->status = Booking::STATUS_NO_SHOW;
            $booking->save();

            BookingHistory::create([
                'booking_id' => $booking->id,
                'admin_id' => null,
                'action' => 'no_show',
                'old_status' => $oldStatus,
                'new_status' => Booking::STATUS_NO_SHOW,
                'notes' => 'ระบบปรับสถานะอัตโนมัติเนื่องจากไม่มาตามนัดหมาย',
            ]);

            Log::info("Marked booking #{$booking->booking_no} as no_show");
            $this->line("Marked booking #{$booking->booking_no} as no_show");
            $count++;
        }

        $this->info("Successfully updated {$count} booking(s) to no_show.");

        return self::SUCCESS;
    }
}

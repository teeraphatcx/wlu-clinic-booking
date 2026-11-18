<?php

namespace App\Services;

use App\Events\BookingCancelled;
use App\Events\BookingCreated;
use App\Events\BookingRescheduled;
use App\Models\Admin;
use App\Models\Booking;
use App\Models\BookingHistory;
use App\Models\ClinicHoliday;
use App\Models\ClinicSchedule;
use App\Models\Customer;
use App\Models\Service;
use App\Models\Setting;
use App\Models\Therapist;
use App\Models\TimeSlot;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class BookingService
{
    public function __construct(
        protected AvailabilityService $availabilityService
    ) {}

    /**
     * Create a new booking with transaction and pessimistic lock.
     *
     *
     * @throws InvalidArgumentException
     */
    public function createBooking(array $data): Booking
    {
        return DB::transaction(function () use ($data) {
            $bookingDateObject = Carbon::parse($data['booking_date'])->startOfDay();
            $bookingDate = $bookingDateObject->toDateString();
            $bookingDateEnd = $bookingDateObject->copy()->endOfDay();
            $timeSlotId = (int) $data['time_slot_id'];
            $serviceId = (int) $data['service_id'];
            $service = Service::findOrFail($serviceId);

            if (! $service->is_active) {
                throw new InvalidArgumentException('บริการนี้ไม่เปิดให้บริการ');
            }

            // Validate the booking window before calendar availability so an
            // out-of-range date always gets the actionable settings message.
            if (! $this->availabilityService->isWithinBookingWindow($bookingDate)) {
                throw new InvalidArgumentException('ไม่สามารถจองเกินระยะเวลาที่กำหนด');
            }

            // Verify Clinic is open on that date.
            $clinicSchedule = $this->availabilityService->isClinicOpen($bookingDate);
            if (! $clinicSchedule instanceof ClinicSchedule) {
                throw new InvalidArgumentException('คลินิกปิดทำการในวันที่เลือก');
            }

            // Verify slot time hasn't passed.
            $slot = TimeSlot::findOrFail($timeSlotId);
            if (! $slot->is_active) {
                throw new InvalidArgumentException('รอบเวลานี้ปิดรับจองแล้ว');
            }
            $serviceEnd = Carbon::parse("{$bookingDate} {$slot->start_time}")->addMinutes((int) $service->duration_minutes);
            if ($slot->start_time < $clinicSchedule->open_time || $serviceEnd->format('H:i:s') > $clinicSchedule->close_time) {
                throw new InvalidArgumentException('ระยะเวลาหัตถการเกินเวลาปิดทำการของคลินิก');
            }
            $partialHoliday = ClinicHoliday::whereBetween('date', [$bookingDateObject, $bookingDateEnd])->where('is_full_day', false)->first();
            if ($partialHoliday && $slot->start_time < $partialHoliday->end_time && $serviceEnd->format('H:i:s') > $partialHoliday->start_time) {
                throw new InvalidArgumentException('รอบเวลานี้ตรงกับช่วงปิดให้บริการ');
            }
            $bookingCloseHours = (int) Setting::get('booking_close_before_hours', 2);
            $slotStartDateTime = Carbon::parse("{$bookingDate} {$slot->start_time}");
            if ($slotStartDateTime->isBefore(Carbon::now()->addHours($bookingCloseHours))) {
                throw new InvalidArgumentException('เลยเวลาเปิดรับจองรอบเวลานี้แล้ว');
            }

            // 4. Verify slot capacity with pessimistic lock
            $maxBookingsPerSlot = $clinicSchedule->max_bookings_per_slot > 0
                ? $clinicSchedule->max_bookings_per_slot
                : (int) Setting::get('default_slot_capacity', 5);

            $currentBookingsCount = Booking::whereBetween('booking_date', [$bookingDateObject, $bookingDateEnd])
                ->where('time_slot_id', $timeSlotId)
                ->whereNotIn('status', [Booking::STATUS_CANCELLED])
                ->lockForUpdate()
                ->count();

            if ($currentBookingsCount >= $maxBookingsPerSlot) {
                throw new InvalidArgumentException('รอบเวลานี้มีผู้จองเต็มแล้ว');
            }

            // 5. Therapist assignment & check
            $therapistId = ! empty($data['therapist_id']) ? (int) $data['therapist_id'] : null;

            if ($service->requires_therapist_selection) {
                if (! $therapistId) {
                    throw new InvalidArgumentException('บริการนี้จำเป็นต้องเลือกแพทย์ผู้ให้บริการ');
                }

                // Check therapist capacity with lock
                $conflict = Booking::where('therapist_id', $therapistId)
                    ->whereBetween('booking_date', [$bookingDateObject, $bookingDateEnd])
                    ->where('time_slot_id', $timeSlotId)
                    ->whereNotIn('status', [Booking::STATUS_CANCELLED])
                    ->lockForUpdate()
                    ->exists();

                if ($conflict || ! $this->availabilityService->isTherapistAvailable($therapistId, $bookingDate, $timeSlotId, $serviceId)) {
                    throw new InvalidArgumentException('แพทย์ท่านนี้ไม่ว่างในรอบเวลาดังกล่าว');
                }
            } else {
                if ($therapistId !== null) {
                    if (! $this->availabilityService->isTherapistAvailable($therapistId, $bookingDate, $timeSlotId, $serviceId)) {
                        throw new InvalidArgumentException('แพทย์ท่านนี้ไม่ว่างในรอบเวลาดังกล่าว');
                    }
                } else {
                    $therapistId = $this->assignTherapist($serviceId, $bookingDate, $timeSlotId);
                    if (! $therapistId) {
                        throw new InvalidArgumentException('ไม่มีแพทย์ว่างในรอบเวลาดังกล่าว');
                    }
                }
            }

            // 6. Find or create customer
            $customerId = $data['customer_id'] ?? null;
            if (! $customerId) {
                $customer = Customer::withTrashed()->where('phone', $data['phone'])->first();
                if ($customer && (! $customer->is_active || $customer->trashed())) {
                    throw new InvalidArgumentException('บัญชีผู้รับบริการนี้ถูกระงับ กรุณาติดต่อคลินิก');
                }
                $customer ??= new Customer(['phone' => $data['phone']]);
                $customer->first_name = $data['first_name'];
                $customer->last_name = $data['last_name'];
                if (! empty($data['email'])) {
                    $customer->email = $data['email'];
                }
                $customer->pdpa_accepted_at = Carbon::now();
                $customer->is_active = true;
                $customer->save();
                $customerId = $customer->id;
            }

            // 7. Check customer max active bookings
            $maxCustomerBookings = (int) Setting::get('max_active_bookings_per_customer', 3);
            $activeCustomerBookings = Booking::where('customer_id', $customerId)
                ->whereIn('status', [Booking::STATUS_PENDING, Booking::STATUS_CONFIRMED])
                ->where('booking_date', '>=', Carbon::today())
                ->count();

            if ($activeCustomerBookings >= $maxCustomerBookings) {
                throw new InvalidArgumentException("ท่านมีคิวที่รอดำเนินการครบกำหนดสูงสุดแล้ว ({$maxCustomerBookings} คิว)");
            }

            // 8. Generate booking number
            $bookingNo = Booking::generateBookingNo($bookingDate);

            // 9. Save booking
            $booking = Booking::create([
                'booking_no' => $bookingNo,
                'customer_id' => $customerId,
                'service_id' => $serviceId,
                'therapist_id' => $therapistId,
                'clinic_schedule_id' => $clinicSchedule->id,
                'time_slot_id' => $timeSlotId,
                'booking_date' => $bookingDate,
                'status' => Booking::STATUS_CONFIRMED,
                'notes' => $data['notes'] ?? null,
                'admin_notes' => null,
                'pdpa_accepted' => true,
                'ip_address' => $data['ip_address'] ?? null,
            ]);

            // 10. Record booking history
            BookingHistory::create([
                'booking_id' => $booking->id,
                'admin_id' => null,
                'action' => 'created',
                'old_status' => null,
                'new_status' => Booking::STATUS_CONFIRMED,
                'new_values' => [
                    'booking_no' => $booking->booking_no,
                    'service_id' => $booking->service_id,
                    'booking_date' => $booking->booking_date->toDateString(),
                    'time_slot_id' => $booking->time_slot_id,
                    'therapist_id' => $booking->therapist_id,
                ],
                'notes' => 'สร้างการจองคิวใหม่ผ่านระบบ',
            ]);

            // 11. Dispatch event
            event(new BookingCreated($booking));

            return $booking;
        });
    }

    /**
     * Auto-assign the therapist who is available and has the lowest load on that date.
     */
    private function assignTherapist(int $serviceId, string $date, int $timeSlotId, ?int $excludeBookingId = null): ?int
    {
        $availableTherapists = $this->availabilityService->getAvailableTherapists($serviceId, $date, $timeSlotId, $excludeBookingId);

        if (empty($availableTherapists)) {
            return null;
        }

        $therapistIds = collect($availableTherapists)->pluck('id')->all();

        // Count bookings for each therapist on this day (excluding cancelled)
        $dateObject = Carbon::parse($date)->startOfDay();
        $dateEnd = $dateObject->copy()->endOfDay();
        $bookingCounts = Booking::whereIn('therapist_id', $therapistIds)
            ->whereBetween('booking_date', [$dateObject, $dateEnd])
            ->when($excludeBookingId, fn ($q) => $q->where('id', '!=', $excludeBookingId))
            ->whereNotIn('status', [Booking::STATUS_CANCELLED])
            ->selectRaw('therapist_id, count(*) as total')
            ->groupBy('therapist_id')
            ->pluck('total', 'therapist_id')
            ->toArray();

        $selectedTherapistId = null;
        $minBookings = PHP_INT_MAX;

        foreach ($therapistIds as $id) {
            $count = $bookingCounts[$id] ?? 0;
            if ($count < $minBookings) {
                $minBookings = $count;
                $selectedTherapistId = $id;
            }
        }

        return $selectedTherapistId;
    }

    /**
     * Cancel a booking.
     *
     * @throws InvalidArgumentException
     */
    public function cancelBooking(Booking $booking, string $reason, ?Admin $admin = null): bool
    {
        if ($admin === null) {
            $canCancel = $this->canCancel($booking);
            if ($canCancel !== true) {
                throw new InvalidArgumentException($canCancel);
            }
        } elseif (in_array($booking->status, [Booking::STATUS_CANCELLED, Booking::STATUS_COMPLETED], true)) {
            throw new InvalidArgumentException('ไม่สามารถยกเลิกการจองในสถานะปัจจุบันได้');
        }

        return DB::transaction(function () use ($booking, $reason, $admin) {
            $oldStatus = $booking->status;

            $booking->status = Booking::STATUS_CANCELLED;
            $booking->cancelled_at = Carbon::now();
            $booking->cancel_reason = $reason;
            $booking->save();

            BookingHistory::create([
                'booking_id' => $booking->id,
                'admin_id' => $admin?->id,
                'action' => 'cancelled',
                'old_status' => $oldStatus,
                'new_status' => Booking::STATUS_CANCELLED,
                'notes' => $reason,
            ]);

            event(new BookingCancelled($booking));

            return true;
        });
    }

    /**
     * Reschedule a booking to a new date, time slot, and optionally therapist.
     *
     * @throws InvalidArgumentException
     */
    public function rescheduleBooking(
        Booking $booking,
        string $newDate,
        int $newTimeSlotId,
        ?int $newTherapistId = null,
        ?Admin $admin = null
    ): Booking {
        if ($admin === null) {
            $canReschedule = $this->canReschedule($booking);
            if ($canReschedule !== true) {
                throw new InvalidArgumentException($canReschedule);
            }
        } elseif (in_array($booking->status, [Booking::STATUS_CANCELLED, Booking::STATUS_COMPLETED], true)) {
            throw new InvalidArgumentException('ไม่สามารถเลื่อนนัดหมายในสถานะปัจจุบันได้');
        }

        return DB::transaction(function () use ($booking, $newDate, $newTimeSlotId, $newTherapistId, $admin) {
            $newBookingDateObject = Carbon::parse($newDate)->startOfDay();
            $newBookingDate = $newBookingDateObject->toDateString();
            $newBookingDateEnd = $newBookingDateObject->copy()->endOfDay();
            $service = $booking->service;

            // 1. Verify Clinic is open
            $clinicSchedule = $this->availabilityService->isClinicOpen($newBookingDate);
            if (! $clinicSchedule instanceof ClinicSchedule) {
                throw new InvalidArgumentException('คลินิกปิดทำการในวันที่เลือก');
            }

            // 2. Verify within booking window
            if (! $this->availabilityService->isWithinBookingWindow($newBookingDate)) {
                throw new InvalidArgumentException('ไม่สามารถเลือกวันที่เกินระยะเวลาที่กำหนด');
            }

            // 3. Verify slot time hasn't passed
            $slot = TimeSlot::findOrFail($newTimeSlotId);
            if (! $slot->is_active) {
                throw new InvalidArgumentException('รอบเวลานี้ปิดรับจองแล้ว');
            }
            $serviceEnd = Carbon::parse("{$newBookingDate} {$slot->start_time}")->addMinutes((int) $service->duration_minutes);
            if ($slot->start_time < $clinicSchedule->open_time || $serviceEnd->format('H:i:s') > $clinicSchedule->close_time) {
                throw new InvalidArgumentException('ระยะเวลาหัตถการเกินเวลาปิดทำการของคลินิก');
            }
            $partialHoliday = ClinicHoliday::whereBetween('date', [$newBookingDateObject, $newBookingDateEnd])->where('is_full_day', false)->first();
            if ($partialHoliday && $slot->start_time < $partialHoliday->end_time && $serviceEnd->format('H:i:s') > $partialHoliday->start_time) {
                throw new InvalidArgumentException('รอบเวลานี้ตรงกับช่วงปิดให้บริการ');
            }
            $bookingCloseHours = (int) Setting::get('booking_close_before_hours', 2);
            $slotStartDateTime = Carbon::parse("{$newBookingDate} {$slot->start_time}");
            if ($slotStartDateTime->isBefore(Carbon::now()->addHours($bookingCloseHours))) {
                throw new InvalidArgumentException('เลยเวลาเปิดรับจองรอบเวลานี้แล้ว');
            }

            // 4. Verify slot capacity (excluding this booking if same slot)
            $maxBookingsPerSlot = $clinicSchedule->max_bookings_per_slot > 0
                ? $clinicSchedule->max_bookings_per_slot
                : (int) Setting::get('default_slot_capacity', 5);

            $currentBookingsCount = Booking::whereBetween('booking_date', [$newBookingDateObject, $newBookingDateEnd])
                ->where('time_slot_id', $newTimeSlotId)
                ->where('id', '!=', $booking->id)
                ->whereNotIn('status', [Booking::STATUS_CANCELLED])
                ->lockForUpdate()
                ->count();

            if ($currentBookingsCount >= $maxBookingsPerSlot) {
                throw new InvalidArgumentException('รอบเวลานี้มีผู้จองเต็มแล้ว');
            }

            // 5. Therapist verification
            $therapistId = $newTherapistId ?: ($service->requires_therapist_selection ? $booking->therapist_id : null);

            if ($service->requires_therapist_selection) {
                if (! $therapistId) {
                    throw new InvalidArgumentException('กรุณาเลือกแพทย์ผู้ให้บริการ');
                }

                $conflict = Booking::where('therapist_id', $therapistId)
                    ->whereBetween('booking_date', [$newBookingDateObject, $newBookingDateEnd])
                    ->where('time_slot_id', $newTimeSlotId)
                    ->where('id', '!=', $booking->id)
                    ->whereNotIn('status', [Booking::STATUS_CANCELLED])
                    ->lockForUpdate()
                    ->exists();

                if ($conflict || ! $this->availabilityService->isTherapistAvailable($therapistId, $newBookingDate, $newTimeSlotId, $service->id, $booking->id)) {
                    throw new InvalidArgumentException('แพทย์ท่านนี้ไม่ว่างในรอบเวลาดังกล่าว');
                }
            } else {
                if ($therapistId !== null) {
                    if (! $this->availabilityService->isTherapistAvailable($therapistId, $newBookingDate, $newTimeSlotId, $service->id, $booking->id)) {
                        throw new InvalidArgumentException('แพทย์ท่านนี้ไม่ว่างในรอบเวลาดังกล่าว');
                    }
                } else {
                    $therapistId = $this->assignTherapist($service->id, $newBookingDate, $newTimeSlotId, $booking->id);
                    if (! $therapistId) {
                        throw new InvalidArgumentException('ไม่มีแพทย์ว่างในรอบเวลาดังกล่าว');
                    }
                }
            }

            $oldValues = [
                'booking_date' => $booking->booking_date->toDateString(),
                'time_slot_id' => $booking->time_slot_id,
                'therapist_id' => $booking->therapist_id,
            ];

            $newValues = [
                'booking_date' => $newBookingDate,
                'time_slot_id' => $newTimeSlotId,
                'therapist_id' => $therapistId,
            ];

            $booking->booking_date = $newBookingDate;
            $booking->time_slot_id = $newTimeSlotId;
            $booking->therapist_id = $therapistId;
            $booking->clinic_schedule_id = $clinicSchedule->id;
            $booking->save();

            BookingHistory::create([
                'booking_id' => $booking->id,
                'admin_id' => $admin?->id,
                'action' => 'rescheduled',
                'old_status' => $booking->status,
                'new_status' => $booking->status,
                'old_values' => $oldValues,
                'new_values' => $newValues,
                'notes' => $admin ? 'เจ้าหน้าที่แก้ไขวันเวลานัดหมาย' : 'ผู้รับบริการเลื่อนวันเวลานัดหมาย',
            ]);

            event(new BookingRescheduled($booking, $oldValues));

            return $booking;
        });
    }

    /**
     * Change booking status (primarily for Admin operations).
     */
    public function changeStatus(Booking $booking, string $newStatus, ?Admin $admin = null, ?string $notes = null): bool
    {
        if (! array_key_exists($newStatus, Booking::STATUSES)) {
            throw new InvalidArgumentException('สถานะไม่ถูกต้อง');
        }

        return DB::transaction(function () use ($booking, $newStatus, $admin, $notes) {
            $oldStatus = $booking->status;
            $booking->status = $newStatus;

            if ($newStatus === Booking::STATUS_CANCELLED) {
                $booking->cancelled_at = Carbon::now();
                $booking->cancel_reason = $notes;
            }

            if ($notes && $admin) {
                $booking->admin_notes = $notes;
            }

            $booking->save();

            BookingHistory::create([
                'booking_id' => $booking->id,
                'admin_id' => $admin?->id,
                'action' => 'status_changed',
                'old_status' => $oldStatus,
                'new_status' => $newStatus,
                'notes' => $notes ?: "เปลี่ยนสถานะจาก {$oldStatus} เป็น {$newStatus}",
            ]);

            if ($newStatus === Booking::STATUS_CANCELLED) {
                event(new BookingCancelled($booking));
            }

            return true;
        });
    }

    /**
     * Check if a booking is eligible for cancellation.
     */
    public function canCancel(Booking $booking): bool|string
    {
        if ($booking->status === Booking::STATUS_CANCELLED) {
            return 'การจองนี้ถูกยกเลิกแล้ว';
        }

        if ($booking->status === Booking::STATUS_COMPLETED) {
            return 'ไม่สามารถยกเลิกการจองที่เสร็จสิ้นแล้ว';
        }

        if ($booking->status === Booking::STATUS_NO_SHOW) {
            return 'ไม่สามารถยกเลิกการจองที่ไม่มาตามนัดหมาย';
        }

        $cancelBeforeHours = (int) Setting::get('cancel_before_hours', 24);
        $timeSlot = $booking->timeSlot;
        $startTime = $timeSlot ? $timeSlot->start_time : '08:00:00';
        $bookingDateTime = Carbon::parse($booking->booking_date->format('Y-m-d').' '.$startTime);

        $deadline = $bookingDateTime->copy()->subHours($cancelBeforeHours);

        if (Carbon::now()->greaterThanOrEqualTo($deadline)) {
            return "ต้องยกเลิกก่อนเวลานัดหมายอย่างน้อย {$cancelBeforeHours} ชั่วโมง";
        }

        return true;
    }

    /**
     * Check if a booking is eligible for rescheduling.
     */
    public function canReschedule(Booking $booking): bool|string
    {
        if ($booking->status === Booking::STATUS_CANCELLED) {
            return 'ไม่สามารถเลื่อนการจองที่ยกเลิกแล้ว';
        }

        if ($booking->status === Booking::STATUS_COMPLETED) {
            return 'ไม่สามารถเลื่อนการจองที่เสร็จสิ้นแล้ว';
        }

        if ($booking->status === Booking::STATUS_NO_SHOW) {
            return 'ไม่สามารถเลื่อนการจองที่ไม่มาตามนัดหมาย';
        }

        $rescheduleBeforeHours = (int) Setting::get('reschedule_before_hours', 24);
        $timeSlot = $booking->timeSlot;
        $startTime = $timeSlot ? $timeSlot->start_time : '08:00:00';
        $bookingDateTime = Carbon::parse($booking->booking_date->format('Y-m-d').' '.$startTime);

        $deadline = $bookingDateTime->copy()->subHours($rescheduleBeforeHours);

        if (Carbon::now()->greaterThanOrEqualTo($deadline)) {
            return "ต้องเลื่อนการจองล่วงหน้าอย่างน้อย {$rescheduleBeforeHours} ชั่วโมง";
        }

        return true;
    }
}

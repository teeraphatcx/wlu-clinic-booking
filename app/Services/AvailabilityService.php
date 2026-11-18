<?php

namespace App\Services;

use App\Models\Booking;
use App\Models\ClinicHoliday;
use App\Models\ClinicSchedule;
use App\Models\Service;
use App\Models\Setting;
use App\Models\Therapist;
use App\Models\TherapistDayOff;
use App\Models\TimeSlot;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class AvailabilityService
{
    /**
     * @var array<string, bool|ClinicSchedule>
     */
    protected array $clinicOpenCache = [];

    /**
     * @var array<string, bool>
     */
    protected array $bookingWindowCache = [];

    /**
     * @var array<string, bool>
     */
    protected array $therapistAvailabilityCache = [];

    /**
     * @var array{booking_enabled: bool, booking_advance_days: int, booking_close_before_hours: int, default_slot_capacity: int}|null
     */
    protected ?array $bookingSettings = null;

    /**
     * Get available dates in a month for a specific service.
     *
     * @return array<string, array<string, bool|int|string|null>>
     */
    public function getAvailableDates(Service|int $service, int $month, int $year): array
    {
        $serviceId = $service instanceof Service ? $service->id : $service;
        $firstDate = Carbon::createFromDate($year, $month, 1)->startOfDay();
        $lastDate = $firstDate->copy()->endOfMonth();
        $today = Carbon::today();
        $settings = $this->bookingSettings();
        $context = $this->buildAvailabilityContext($service, $firstDate->toDateString(), $lastDate->toDateString());
        $dates = [];

        for ($day = 1; $day <= $lastDate->day; $day++) {
            $dateObject = $firstDate->copy()->day($day);
            $date = $dateObject->toDateString();

            if ($dateObject->isBefore($today)) {
                $dates[$date] = $this->dateStatus($dateObject, 'closed', false, 'วันที่ผ่านมาแล้ว');

                continue;
            }

            if (! $settings['booking_enabled'] || ! $this->isWithinBookingWindowUsing($dateObject, $settings)) {
                $dates[$date] = $this->dateStatus($dateObject, 'closed', false, 'เกินระยะเวลาที่เปิดให้จองล่วงหน้า');

                continue;
            }

            if ($this->holidayForDate($context, $date, true)) {
                $holiday = $this->holidayForDate($context, $date, true);
                $dates[$date] = $this->dateStatus($dateObject, 'holiday', false, $holiday?->name);

                continue;
            }

            $clinicSchedule = $context['clinicSchedules']->get($dateObject->dayOfWeek);
            if (! $clinicSchedule || ! $clinicSchedule->is_open) {
                $dates[$date] = $this->dateStatus($dateObject, 'closed', false, 'คลินิกปิดทำการ');

                continue;
            }

            $slots = $this->calculateAvailableSlots($context, $date);
            $hasAvailableSlot = collect($slots)->contains(fn (array $slot): bool => $slot['status'] === 'available');
            $allSlotsFull = $slots !== [] && ! collect($slots)->contains(fn (array $slot): bool => $slot['status'] !== 'full');

            if ($hasAvailableSlot) {
                $status = 'available';
                $reason = null;
            } elseif ($allSlotsFull) {
                $status = 'full';
                $reason = 'คิวเต็มทุกรอบเวลา';
            } else {
                $status = 'closed';
                $reason = 'ไม่มีรอบเวลาเปิดให้บริการ';
            }

            $dates[$date] = $this->dateStatus($dateObject, $status, $status === 'available', $reason);
        }

        return $dates;
    }

    /**
     * Get available time slots for a specific date and service.
     *
     * @return array<int, array<string, bool|int|string|null>>
     */
    public function getAvailableSlots(Service|int $service, string $date, ?int $therapistId = null): array
    {
        $context = $this->buildAvailabilityContext($service, $date, $date);

        return $this->calculateAvailableSlots($context, $date, $therapistId);
    }

    /**
     * Get therapists who can provide the service, work on the date, and have no conflicts.
     *
     * @return array<int, Therapist>
     */
    public function getAvailableTherapists(Service|int $service, string $date, int $timeSlotId, ?int $excludeBookingId = null): array
    {
        $context = $this->buildAvailabilityContext($service, $date, $date);
        $slot = $context['timeSlots']->firstWhere('id', $timeSlotId);

        if (! $slot) {
            return [];
        }

        return $this->availableTherapistsFromContext($context, $date, $slot, $excludeBookingId)->all();
    }

    /**
     * Check if a specific therapist is available on a date and time slot.
     */
    public function isTherapistAvailable(int $therapistId, string $date, int $timeSlotId, ?int $serviceId = null, ?int $excludeBookingId = null): bool
    {
        $dateStart = Carbon::parse($date)->startOfDay();
        $dateEnd = $dateStart->copy()->endOfDay();
        $cacheKey = implode('|', [$therapistId, $date, $timeSlotId, $serviceId ?? 'any', $excludeBookingId ?? 'none']);
        if (array_key_exists($cacheKey, $this->therapistAvailabilityCache)) {
            return $this->therapistAvailabilityCache[$cacheKey];
        }

        $slot = TimeSlot::find($timeSlotId);
        if (! $slot) {
            return $this->therapistAvailabilityCache[$cacheKey] = false;
        }

        if ($serviceId !== null) {
            $context = $this->buildAvailabilityContext($serviceId, $date, $date);
            $therapist = $context['service']->therapists->firstWhere('id', $therapistId);

            if (! $therapist) {
                return $this->therapistAvailabilityCache[$cacheKey] = false;
            }

            return $this->therapistAvailabilityCache[$cacheKey] = $this->isTherapistAvailableFromContext($context, $therapist, $date, $slot, $excludeBookingId);
        }

        $therapist = Therapist::query()
            ->whereKey($therapistId)
            ->where('is_active', true)
            ->with([
                'schedules',
                'daysOff' => fn (Builder $query): Builder => $query->whereBetween('date', [$dateStart, $dateEnd]),
                'blockedPeriods' => fn (Builder $query): Builder => $query
                    ->where('start_datetime', '<', $dateEnd)
                    ->where('end_datetime', '>', $dateStart),
            ])
            ->first();

        if (! $therapist) {
            return $this->therapistAvailabilityCache[$cacheKey] = false;
        }

        $bookings = Booking::query()
            ->select(['id', 'service_id', 'therapist_id', 'time_slot_id', 'booking_date', 'status'])
            ->where('therapist_id', $therapistId)
            ->whereBetween('booking_date', [$dateStart, $dateEnd])
            ->whereNotIn('status', [Booking::STATUS_CANCELLED])
            ->with(['timeSlot:id,start_time', 'service:id,duration_minutes'])
            ->get();

        $result = $this->isTherapistAvailableForModels($therapist, null, $slot, $date, $bookings, $excludeBookingId);

        return $this->therapistAvailabilityCache[$cacheKey] = $result;
    }

    /**
     * Check if clinic is open on a given date.
     * Returns ClinicSchedule if open, false if closed or full-day holiday.
     */
    public function isClinicOpen(string $date): bool|ClinicSchedule
    {
        if (array_key_exists($date, $this->clinicOpenCache)) {
            return $this->clinicOpenCache[$date];
        }

        $dateStart = Carbon::parse($date)->startOfDay();
        $dateEnd = $dateStart->copy()->endOfDay();

        if (ClinicHoliday::whereBetween('date', [$dateStart, $dateEnd])->where('is_full_day', true)->exists()) {
            return $this->clinicOpenCache[$date] = false;
        }

        $schedule = ClinicSchedule::allCached()->firstWhere('day_of_week', Carbon::parse($date)->dayOfWeek);

        return $this->clinicOpenCache[$date] = $schedule && $schedule->is_open ? $schedule : false;
    }

    /**
     * Check if date is within allowed advance booking window.
     */
    public function isWithinBookingWindow(string $date): bool
    {
        if (array_key_exists($date, $this->bookingWindowCache)) {
            return $this->bookingWindowCache[$date];
        }

        $result = $this->isWithinBookingWindowUsing(Carbon::parse($date)->startOfDay(), $this->bookingSettings());

        return $this->bookingWindowCache[$date] = $result;
    }

    /**
     * @return array{
     *     service: Service,
     *     timeSlots: Collection,
     *     clinicSchedules: Collection,
     *     holidays: Collection,
     *     bookings: Collection,
     *     settings: array{booking_enabled: bool, booking_advance_days: int, booking_close_before_hours: int, default_slot_capacity: int}
     * }
     */
    private function buildAvailabilityContext(Service|int $service, string $fromDate, string $toDate): array
    {
        $from = Carbon::parse($fromDate)->startOfDay();
        $to = Carbon::parse($toDate)->endOfDay();

        $service = $service instanceof Service
            ? $service
            : Service::query()
                ->select(['id', 'duration_minutes', 'requires_therapist_selection'])
                ->findOrFail($service);

        $service->load([
            'therapists' => fn ($query) => $query
                ->select(['therapists.id', 'first_name', 'last_name', 'nickname', 'gender', 'bio', 'photo', 'is_active'])
                ->where('is_active', true),
            'therapists.schedules',
            'therapists.daysOff' => fn ($query) => $query->whereBetween('date', [$from, $to]),
            'therapists.blockedPeriods' => fn ($query) => $query
                ->where('start_datetime', '<', $to)
                ->where('end_datetime', '>', $from),
        ]);

        $holidays = ClinicHoliday::query()
            ->whereBetween('date', [$from, $to])
            ->get()
            ->groupBy(fn (ClinicHoliday $holiday): string => $holiday->date->toDateString());

        $bookings = Booking::query()
            ->select(['id', 'service_id', 'therapist_id', 'time_slot_id', 'booking_date', 'status'])
            ->whereBetween('booking_date', [$from, $to])
            ->whereNotIn('status', [Booking::STATUS_CANCELLED])
            ->with(['timeSlot:id,start_time', 'service:id,duration_minutes'])
            ->get()
            ->groupBy(fn (Booking $booking): string => $booking->booking_date->toDateString());

        return [
            'service' => $service,
            'timeSlots' => TimeSlot::active()->get(),
            'clinicSchedules' => ClinicSchedule::allCached()->keyBy('day_of_week'),
            'holidays' => $holidays,
            'bookings' => $bookings,
            'settings' => $this->bookingSettings(),
        ];
    }

    /**
     * @param array{
     *     service: Service,
     *     timeSlots: Collection,
     *     clinicSchedules: Collection,
     *     holidays: Collection,
     *     bookings: Collection,
     *     settings: array{booking_enabled: bool, booking_advance_days: int, booking_close_before_hours: int, default_slot_capacity: int}
     * } $context
     * @return array<int, array<string, bool|int|string|null>>
     */
    private function calculateAvailableSlots(array $context, string $date, ?int $therapistId = null): array
    {
        $service = $context['service'];
        $schedule = $context['clinicSchedules']->get(Carbon::parse($date)->dayOfWeek);
        $partialHoliday = $this->holidayForDate($context, $date, false);
        $bookingsBySlot = $context['bookings']->get($date, collect())->groupBy('time_slot_id');
        $settings = $context['settings'];
        $isWithinWindow = $this->isWithinBookingWindowUsing(Carbon::parse($date)->startOfDay(), $settings);
        $earliestAllowedTime = Carbon::now()->addHours($settings['booking_close_before_hours']);
        $maxBookingsPerSlot = $schedule instanceof ClinicSchedule && $schedule->max_bookings_per_slot > 0
            ? $schedule->max_bookings_per_slot
            : $settings['default_slot_capacity'];
        $slots = $context['timeSlots'];
        $result = [];

        foreach ($slots as $slot) {
            $status = 'available';
            $reason = null;
            $slotStartDateTime = Carbon::parse("{$date} {$slot->start_time}");
            $slotEndDateTime = $slotStartDateTime->copy()->addMinutes((int) $service->duration_minutes);

            if (! $settings['booking_enabled']) {
                $status = 'unavailable';
                $reason = 'ระบบปิดรับจองออนไลน์';
            } elseif (! $schedule) {
                $status = 'unavailable';
                $reason = 'คลินิกปิดทำการ';
            } elseif (! $isWithinWindow) {
                $status = 'unavailable';
                $reason = 'อยู่นอกช่วงเวลาที่เปิดรับจอง';
            } elseif ($slotStartDateTime->isBefore($earliestAllowedTime)) {
                $status = 'unavailable';
                $reason = 'เลยเวลาเปิดรับจองแล้ว';
            } elseif ($slot->start_time < $schedule->open_time || $slotEndDateTime->format('H:i:s') > $schedule->close_time) {
                $status = 'unavailable';
                $reason = 'อยู่นอกเวลาเปิดทำการ';
            } elseif ($partialHoliday && $slotStartDateTime->format('H:i:s') < $partialHoliday->end_time && $slotEndDateTime->format('H:i:s') > $partialHoliday->start_time) {
                $status = 'unavailable';
                $reason = $partialHoliday->name;
            }

            $bookedCount = $bookingsBySlot->get($slot->id, collect())->count();
            $remainingCapacity = max(0, $maxBookingsPerSlot - $bookedCount);

            if ($status === 'available' && $remainingCapacity <= 0) {
                $status = 'full';
                $reason = 'คิวเต็มแล้ว';
            }

            $availableTherapistsCount = 0;
            if ($status === 'available') {
                if ($therapistId !== null) {
                    if (! $this->availableTherapistsFromContext($context, $date, $slot, null)->contains('id', $therapistId)) {
                        $status = 'full';
                        $reason = 'แพทย์ที่เลือกไม่ว่างในรอบเวลานี้';
                    } else {
                        $availableTherapistsCount = 1;
                    }
                } else {
                    $availableTherapistsCount = $this->availableTherapistsFromContext($context, $date, $slot, null)->count();
                    if ($availableTherapistsCount === 0) {
                        $status = 'full';
                        $reason = 'ไม่มีแพทย์ว่างในรอบเวลานี้';
                    }
                }
            }

            $result[] = [
                'id' => $slot->id,
                'slot_number' => $slot->slot_number,
                'start_time' => substr($slot->start_time, 0, 5),
                'end_time' => substr($slot->end_time, 0, 5),
                'label_th' => $slot->label_th,
                'status' => $status,
                'is_available' => $status === 'available',
                'remaining_capacity' => $remainingCapacity,
                'max_capacity' => $maxBookingsPerSlot,
                'available_therapists_count' => $availableTherapistsCount,
                'reason' => $reason,
            ];
        }

        return $result;
    }

    /**
     * @param array{
     *     service: Service,
     *     timeSlots: Collection,
     *     clinicSchedules: Collection,
     *     holidays: Collection,
     *     bookings: Collection,
     *     settings: array{booking_enabled: bool, booking_advance_days: int, booking_close_before_hours: int, default_slot_capacity: int}
     * } $context
     */
    private function availableTherapistsFromContext(array $context, string $date, TimeSlot $slot, ?int $excludeBookingId): Collection
    {
        return $context['service']->therapists
            ->filter(fn (Therapist $therapist): bool => $this->isTherapistAvailableFromContext($context, $therapist, $date, $slot, $excludeBookingId))
            ->values();
    }

    /**
     * @param array{
     *     service: Service,
     *     timeSlots: Collection,
     *     clinicSchedules: Collection,
     *     holidays: Collection,
     *     bookings: Collection,
     *     settings: array{booking_enabled: bool, booking_advance_days: int, booking_close_before_hours: int, default_slot_capacity: int}
     * } $context
     */
    private function isTherapistAvailableFromContext(array $context, Therapist $therapist, string $date, TimeSlot $slot, ?int $excludeBookingId): bool
    {
        return $this->isTherapistAvailableForModels(
            $therapist,
            $context['service'],
            $slot,
            $date,
            $context['bookings']->get($date, collect()),
            $excludeBookingId,
        );
    }

    private function isTherapistAvailableForModels(Therapist $therapist, ?Service $service, TimeSlot $slot, string $date, Collection $bookings, ?int $excludeBookingId): bool
    {
        $dayOfWeek = Carbon::parse($date)->dayOfWeek;
        $schedule = $therapist->schedules->firstWhere('day_of_week', $dayOfWeek);

        if (! $schedule || ! $schedule->is_working) {
            return false;
        }

        $duration = (int) ($service?->duration_minutes ?? 60);
        $slotStart = Carbon::parse("{$date} {$slot->start_time}");
        $slotEnd = $slotStart->copy()->addMinutes($duration);

        if ($slot->start_time < $schedule->start_time || $slotEnd->format('H:i:s') > $schedule->end_time) {
            return false;
        }

        if ($schedule->break_start && $schedule->break_end && $slot->start_time < $schedule->break_end && $slotEnd->format('H:i:s') > $schedule->break_start) {
            return false;
        }

        if ($therapist->daysOff->contains(fn (TherapistDayOff $dayOff): bool => $dayOff->date->toDateString() === $date)) {
            return false;
        }

        if ($therapist->blockedPeriods->contains(fn ($blockedPeriod): bool => $blockedPeriod->start_datetime->lt($slotEnd) && $blockedPeriod->end_datetime->gt($slotStart))) {
            return false;
        }

        return ! $bookings
            ->where('therapist_id', $therapist->id)
            ->when($excludeBookingId, fn (Collection $items): Collection => $items->where('id', '!=', $excludeBookingId))
            ->contains(function (Booking $booking) use ($slotStart, $slotEnd): bool {
                if (! $booking->timeSlot) {
                    return false;
                }

                $existingStart = Carbon::parse($booking->booking_date->toDateString().' '.$booking->timeSlot->start_time);
                $existingEnd = $existingStart->copy()->addMinutes((int) ($booking->service?->duration_minutes ?? 60));

                return $slotStart->lt($existingEnd) && $slotEnd->gt($existingStart);
            });
    }

    /**
     * @param  array{holidays: Collection}  $context
     */
    private function holidayForDate(array $context, string $date, ?bool $fullDay): ?ClinicHoliday
    {
        return $context['holidays']->get($date, collect())->first(
            fn (ClinicHoliday $holiday): bool => $fullDay === null || $holiday->is_full_day === $fullDay
        );
    }

    /**
     * @param  array{booking_enabled: bool, booking_advance_days: int, booking_close_before_hours: int, default_slot_capacity: int}  $settings
     */
    private function isWithinBookingWindowUsing(Carbon $target, array $settings): bool
    {
        if (! $settings['booking_enabled']) {
            return false;
        }

        $today = Carbon::today();

        return ! $target->isBefore($today) && $target->lessThanOrEqualTo($today->copy()->addDays($settings['booking_advance_days']));
    }

    /**
     * @return array{booking_enabled: bool, booking_advance_days: int, booking_close_before_hours: int, default_slot_capacity: int}
     */
    private function bookingSettings(): array
    {
        return $this->bookingSettings ??= [
            'booking_enabled' => (bool) Setting::get('booking_enabled', true),
            'booking_advance_days' => (int) Setting::get('booking_advance_days', 30),
            'booking_close_before_hours' => (int) Setting::get('booking_close_before_hours', 2),
            'default_slot_capacity' => (int) Setting::get('default_slot_capacity', 5),
        ];
    }

    /**
     * @return array<string, bool|int|string|null>
     */
    private function dateStatus(Carbon $date, string $status, bool $selectable, ?string $reason): array
    {
        return [
            'date' => $date->toDateString(),
            'day' => $date->day,
            'day_of_week' => $date->dayOfWeek,
            'status' => $status,
            'is_selectable' => $selectable,
            'reason' => $reason,
        ];
    }
}

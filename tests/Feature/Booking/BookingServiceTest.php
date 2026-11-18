<?php

namespace Tests\Feature\Booking;

use App\Models\Booking;
use App\Models\ClinicHoliday;
use App\Models\ClinicSchedule;
use App\Models\Customer;
use App\Models\Service;
use App\Models\Setting;
use App\Models\Therapist;
use App\Models\TimeSlot;
use App\Services\BookingService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use InvalidArgumentException;
use Tests\TestCase;

class BookingServiceTest extends TestCase
{
    use RefreshDatabase;

    protected BookingService $bookingService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
        $this->bookingService = app(BookingService::class);
    }

    /**
     * Helper to get a valid open booking date in the near future (e.g., next Tuesday).
     */
    protected function getValidBookingDate(): Carbon
    {
        $date = Carbon::today()->addDays(3);
        while ($date->isWeekend() || ClinicHoliday::where('date', $date->toDateString())->where('is_full_day', true)->exists()) {
            $date->addDay();
        }

        return $date;
    }

    public function test_can_create_booking_successfully(): void
    {
        $date = $this->getValidBookingDate()->toDateString();
        $service = Service::where('code', 'TTM-MASSAGE-60')->first();
        $slot = TimeSlot::where('slot_number', 2)->first();
        $therapist = $service->therapists()->first();

        $booking = $this->bookingService->createBooking([
            'service_id' => $service->id,
            'booking_date' => $date,
            'time_slot_id' => $slot->id,
            'therapist_id' => $therapist->id,
            'first_name' => 'สมศักดิ์',
            'last_name' => 'มีชัย',
            'phone' => '0899998888',
            'email' => 'somsak@example.com',
            'notes' => 'ปวดหลังช่วงล่าง',
            'pdpa_accepted' => true,
        ]);

        $this->assertInstanceOf(Booking::class, $booking);
        $this->assertEquals(Booking::STATUS_CONFIRMED, $booking->status);
        $this->assertEquals($date, $booking->booking_date->toDateString());
        $this->assertEquals($therapist->id, $booking->therapist_id);
        $this->assertDatabaseHas('customers', ['phone' => '0899998888']);
        $this->assertDatabaseHas('bookings', ['id' => $booking->id, 'booking_no' => $booking->booking_no]);
        $this->assertDatabaseHas('booking_histories', ['booking_id' => $booking->id, 'action' => 'created']);
    }

    public function test_booking_number_is_unique(): void
    {
        $date = $this->getValidBookingDate()->toDateString();
        $service = Service::where('code', 'TTM-MASSAGE-60')->first();
        $slot1 = TimeSlot::where('slot_number', 2)->first();
        $slot2 = TimeSlot::where('slot_number', 3)->first();
        $therapist = $service->therapists()->first();

        $booking1 = $this->bookingService->createBooking([
            'service_id' => $service->id,
            'booking_date' => $date,
            'time_slot_id' => $slot1->id,
            'therapist_id' => $therapist->id,
            'first_name' => 'ลูกค้า1',
            'last_name' => 'ใจดี',
            'phone' => '0811111111',
            'pdpa_accepted' => true,
        ]);

        $booking2 = $this->bookingService->createBooking([
            'service_id' => $service->id,
            'booking_date' => $date,
            'time_slot_id' => $slot2->id,
            'therapist_id' => $therapist->id,
            'first_name' => 'ลูกค้า2',
            'last_name' => 'มั่งมี',
            'phone' => '0822222222',
            'pdpa_accepted' => true,
        ]);

        $this->assertNotEquals($booking1->booking_no, $booking2->booking_no);
        $this->assertStringStartsWith('WLU-', $booking1->booking_no);
        $this->assertStringStartsWith('WLU-', $booking2->booking_no);
    }

    public function test_cannot_double_book_same_slot_therapist(): void
    {
        $date = $this->getValidBookingDate()->toDateString();
        $service = Service::where('code', 'TTM-MASSAGE-60')->first();
        $slot = TimeSlot::where('slot_number', 2)->first();
        $therapist = $service->therapists()->first();

        $this->bookingService->createBooking([
            'service_id' => $service->id,
            'booking_date' => $date,
            'time_slot_id' => $slot->id,
            'therapist_id' => $therapist->id,
            'first_name' => 'ลูกค้าคนแรก',
            'last_name' => 'ใจงาม',
            'phone' => '0833333333',
            'pdpa_accepted' => true,
        ]);

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('แพทย์ท่านนี้ไม่ว่างในรอบเวลาดังกล่าว');

        $this->bookingService->createBooking([
            'service_id' => $service->id,
            'booking_date' => $date,
            'time_slot_id' => $slot->id,
            'therapist_id' => $therapist->id,
            'first_name' => 'ลูกค้าคนที่สอง',
            'last_name' => 'สายตรง',
            'phone' => '0844444444',
            'pdpa_accepted' => true,
        ]);
    }

    public function test_auto_assigns_therapist_when_not_required(): void
    {
        $date = $this->getValidBookingDate()->toDateString();
        // TTM-HERBAL-STEAM does not require therapist selection
        $service = Service::where('code', 'TTM-HERBAL-STEAM')->first();
        $slot = TimeSlot::where('slot_number', 2)->first();

        $booking = $this->bookingService->createBooking([
            'service_id' => $service->id,
            'booking_date' => $date,
            'time_slot_id' => $slot->id,
            'therapist_id' => null,
            'first_name' => 'สมร',
            'last_name' => 'ใจเพียร',
            'phone' => '0855555555',
            'pdpa_accepted' => true,
        ]);

        $this->assertNotNull($booking->therapist_id);
        $assignedTherapist = Therapist::find($booking->therapist_id);
        $this->assertNotNull($assignedTherapist);
        $this->assertTrue($assignedTherapist->services->contains($service->id));
    }

    public function test_can_cancel_booking_before_deadline(): void
    {
        $date = $this->getValidBookingDate()->toDateString();
        $service = Service::where('code', 'TTM-MASSAGE-60')->first();
        $slot = TimeSlot::where('slot_number', 2)->first();
        $therapist = $service->therapists()->first();

        $booking = $this->bookingService->createBooking([
            'service_id' => $service->id,
            'booking_date' => $date,
            'time_slot_id' => $slot->id,
            'therapist_id' => $therapist->id,
            'first_name' => 'สมศรี',
            'last_name' => 'ดีเสมอ',
            'phone' => '0866666666',
            'pdpa_accepted' => true,
        ]);

        $result = $this->bookingService->cancelBooking($booking, 'ติดภารกิจกระทันหัน');

        $this->assertTrue($result);
        $booking->refresh();
        $this->assertEquals(Booking::STATUS_CANCELLED, $booking->status);
        $this->assertNotNull($booking->cancelled_at);
        $this->assertEquals('ติดภารกิจกระทันหัน', $booking->cancel_reason);
        $this->assertDatabaseHas('booking_histories', [
            'booking_id' => $booking->id,
            'action' => 'cancelled',
            'new_status' => Booking::STATUS_CANCELLED,
        ]);
    }

    public function test_cannot_cancel_booking_after_deadline(): void
    {
        Setting::set('cancel_before_hours', 24);

        $date = Carbon::today()->toDateString();
        $slot = TimeSlot::where('slot_number', 2)->first(); // 09:00 - 10:00
        $service = Service::where('code', 'TTM-MASSAGE-60')->first();
        $therapist = $service->therapists()->first();

        // Simulate booking today with appointment only 1 hour ahead
        $appointmentTime = Carbon::createFromFormat('Y-m-d H:i:s', "{$date} 09:00:00");
        Carbon::setTestNow($appointmentTime->copy()->subHours(2)); // Only 2 hours before, but cancel_before_hours is 24

        $customer = Customer::create([
            'first_name' => 'วิทย์',
            'last_name' => 'แซ่ลิ้ม',
            'phone' => '0877777777',
        ]);

        $clinicSchedule = ClinicSchedule::where('day_of_week', Carbon::parse($date)->dayOfWeek)->first();

        $booking = Booking::create([
            'booking_no' => Booking::generateBookingNo($date),
            'customer_id' => $customer->id,
            'service_id' => $service->id,
            'therapist_id' => $therapist->id,
            'clinic_schedule_id' => $clinicSchedule?->id ?? 1,
            'time_slot_id' => $slot->id,
            'booking_date' => $date,
            'status' => Booking::STATUS_CONFIRMED,
            'pdpa_accepted' => true,
        ]);

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('ต้องยกเลิกก่อนเวลานัดหมายอย่างน้อย 24 ชั่วโมง');

        $this->bookingService->cancelBooking($booking, 'ขอยกเลิก');

        Carbon::setTestNow();
    }

    public function test_can_reschedule_booking(): void
    {
        $date1 = $this->getValidBookingDate()->toDateString();
        $date2 = Carbon::parse($date1)->addDays(1);
        while ($date2->isWeekend() || ClinicHoliday::where('date', $date2->toDateString())->where('is_full_day', true)->exists()) {
            $date2->addDay();
        }
        $newDate = $date2->toDateString();

        $service = Service::where('code', 'TTM-MASSAGE-60')->first();
        $slot1 = TimeSlot::where('slot_number', 2)->first();
        $slot2 = TimeSlot::where('slot_number', 4)->first();
        $therapist = $service->therapists()->first();

        $booking = $this->bookingService->createBooking([
            'service_id' => $service->id,
            'booking_date' => $date1,
            'time_slot_id' => $slot1->id,
            'therapist_id' => $therapist->id,
            'first_name' => 'นพพล',
            'last_name' => 'เกียรติ',
            'phone' => '0888888888',
            'pdpa_accepted' => true,
        ]);

        $updatedBooking = $this->bookingService->rescheduleBooking(
            $booking,
            $newDate,
            $slot2->id,
            $therapist->id
        );

        $this->assertEquals($newDate, $updatedBooking->booking_date->toDateString());
        $this->assertEquals($slot2->id, $updatedBooking->time_slot_id);
        $this->assertDatabaseHas('booking_histories', [
            'booking_id' => $booking->id,
            'action' => 'rescheduled',
        ]);
    }

    public function test_can_reschedule_booking_on_same_date_different_slot(): void
    {
        $date = $this->getValidBookingDate()->toDateString();
        $service = Service::where('code', 'TTM-MASSAGE-60')->first();
        $slot1 = TimeSlot::where('slot_number', 2)->first();
        $slot2 = TimeSlot::where('slot_number', 4)->first();
        $therapist = $service->therapists()->first();

        $booking = $this->bookingService->createBooking([
            'service_id' => $service->id,
            'booking_date' => $date,
            'time_slot_id' => $slot1->id,
            'therapist_id' => $therapist->id,
            'first_name' => 'วิชัย',
            'last_name' => 'คงดี',
            'phone' => '0877777777',
            'pdpa_accepted' => true,
        ]);

        $updatedBooking = $this->bookingService->rescheduleBooking(
            $booking,
            $date,
            $slot2->id,
            $therapist->id
        );

        $this->assertEquals($date, $updatedBooking->booking_date->toDateString());
        $this->assertEquals($slot2->id, $updatedBooking->time_slot_id);
        $this->assertDatabaseHas('booking_histories', [
            'booking_id' => $booking->id,
            'action' => 'rescheduled',
        ]);
    }

    public function test_cannot_book_on_holiday(): void
    {
        $holidayDate = $this->getValidBookingDate()->toDateString();
        ClinicHoliday::create([
            'date' => $holidayDate,
            'name' => 'วันหยุดพิเศษทดสอบ',
            'is_full_day' => true,
        ]);

        $service = Service::where('code', 'TTM-MASSAGE-60')->first();
        $slot = TimeSlot::where('slot_number', 2)->first();
        $therapist = $service->therapists()->first();

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('คลินิกปิดทำการในวันที่เลือก');

        $this->bookingService->createBooking([
            'service_id' => $service->id,
            'booking_date' => $holidayDate,
            'time_slot_id' => $slot->id,
            'therapist_id' => $therapist->id,
            'first_name' => 'ประทีป',
            'last_name' => 'สว่าง',
            'phone' => '0890001111',
            'pdpa_accepted' => true,
        ]);
    }

    public function test_cannot_book_outside_clinic_hours(): void
    {
        // Sunday (day_of_week = 0) is closed in ClinicScheduleSeeder
        $sunday = Carbon::today()->next(Carbon::SUNDAY)->toDateString();
        $service = Service::where('code', 'TTM-MASSAGE-60')->first();
        $slot = TimeSlot::where('slot_number', 2)->first();
        $therapist = $service->therapists()->first();

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('คลินิกปิดทำการในวันที่เลือก');

        $this->bookingService->createBooking([
            'service_id' => $service->id,
            'booking_date' => $sunday,
            'time_slot_id' => $slot->id,
            'therapist_id' => $therapist->id,
            'first_name' => 'อาทิตย์',
            'last_name' => 'หยุดงาน',
            'phone' => '0892223333',
            'pdpa_accepted' => true,
        ]);
    }

    public function test_cannot_book_beyond_advance_days(): void
    {
        Setting::set('booking_advance_days', 30);

        $farFutureDate = Carbon::today()->addDays(45)->toDateString();
        $service = Service::where('code', 'TTM-MASSAGE-60')->first();
        $slot = TimeSlot::where('slot_number', 2)->first();
        $therapist = $service->therapists()->first();

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('ไม่สามารถจองเกินระยะเวลาที่กำหนด');

        $this->bookingService->createBooking([
            'service_id' => $service->id,
            'booking_date' => $farFutureDate,
            'time_slot_id' => $slot->id,
            'therapist_id' => $therapist->id,
            'first_name' => 'ล่วงหน้า',
            'last_name' => 'เกินไป',
            'phone' => '0894445555',
            'pdpa_accepted' => true,
        ]);
    }
}

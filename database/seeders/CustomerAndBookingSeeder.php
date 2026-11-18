<?php

namespace Database\Seeders;

use App\Models\Admin;
use App\Models\AuditLog;
use App\Models\Booking;
use App\Models\BookingHistory;
use App\Models\ClinicSchedule;
use App\Models\Customer;
use App\Models\Service;
use App\Models\Therapist;
use App\Models\TimeSlot;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class CustomerAndBookingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $customer1 = Customer::updateOrCreate(
            ['phone' => '0891112233'],
            [
                'hn' => 'HN-69001',
                'first_name' => 'วิชัย',
                'last_name' => 'รุ่งเรืองกิจ',
                'email' => 'wichai@example.com',
                'gender' => 'male',
                'birthdate' => '1985-05-20',
                'address' => 'อ.เมือง จ.นครศรีธรรมราช',
                'pdpa_accepted_at' => Carbon::now()->subDays(5),
                'is_active' => true,
                'notes' => 'มีอาการตึงไหล่ข้างขวาเรื้อรัง',
            ]
        );

        $customer2 = Customer::updateOrCreate(
            ['phone' => '0894445566'],
            [
                'hn' => 'HN-69002',
                'first_name' => 'กานดา',
                'last_name' => 'ศรีวิไล',
                'email' => 'kanda@example.com',
                'gender' => 'female',
                'birthdate' => '1992-08-14',
                'address' => 'อ.ท่าศาลา จ.นครศรีธรรมราช',
                'pdpa_accepted_at' => Carbon::now()->subDays(3),
                'is_active' => true,
                'notes' => 'มารดาหลังคลอด 2 เดือน',
            ]
        );

        $service1 = Service::where('code', 'TTM-MASSAGE-60')->first();
        $service2 = Service::where('code', 'TTM-SALT-POT')->first();
        $therapist1 = Therapist::where('code', 'TH-001')->first();
        $therapist2 = Therapist::where('code', 'TH-003')->first();
        $slot1 = TimeSlot::where('slot_number', 2)->first(); // 09:00 - 10:00
        $slot2 = TimeSlot::where('slot_number', 6)->first(); // 13:00 - 14:00
        $clinicSchedule = ClinicSchedule::where('day_of_week', 1)->first(); // Monday
        $admin = Admin::where('email', 'admin@wlu-clinic.local')->first();

        // Sample Booking 1
        $bookingDate1 = Carbon::tomorrow()->isWeekend() ? Carbon::today()->next(Carbon::MONDAY) : Carbon::tomorrow();
        $bookingNo1 = Booking::generateBookingNo($bookingDate1);

        $booking1 = Booking::updateOrCreate(
            ['booking_no' => $bookingNo1],
            [
                'customer_id' => $customer1->id,
                'service_id' => $service1 ? $service1->id : 1,
                'therapist_id' => $therapist1 ? $therapist1->id : null,
                'clinic_schedule_id' => $clinicSchedule ? $clinicSchedule->id : null,
                'time_slot_id' => $slot1 ? $slot1->id : 1,
                'booking_date' => $bookingDate1->toDateString(),
                'status' => Booking::STATUS_CONFIRMED,
                'notes' => 'ขอหมอที่เน้นแก้จุดสะบักขวา',
                'admin_notes' => 'ยืนยันการจองเรียบร้อยทางโทรศัพท์',
                'pdpa_accepted' => true,
                'ip_address' => '127.0.0.1',
            ]
        );

        // Record booking history
        BookingHistory::create([
            'booking_id' => $booking1->id,
            'admin_id' => $admin ? $admin->id : null,
            'action' => 'confirmed',
            'old_status' => Booking::STATUS_PENDING,
            'new_status' => Booking::STATUS_CONFIRMED,
            'notes' => 'เจ้าหน้าที่ยืนยันการจองคิว',
        ]);

        // Audit Log
        if ($admin) {
            AuditLog::record(
                $admin->id,
                'booking_confirm',
                $booking1,
                ['status' => Booking::STATUS_PENDING],
                ['status' => Booking::STATUS_CONFIRMED]
            );
        }
    }
}

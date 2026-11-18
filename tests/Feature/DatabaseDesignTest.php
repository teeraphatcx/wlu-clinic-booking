<?php

namespace Tests\Feature;

use App\Models\Admin;
use App\Models\AuditLog;
use App\Models\Booking;
use App\Models\ClinicSchedule;
use App\Models\Permission;
use App\Models\Role;
use App\Models\Service;
use App\Models\Setting;
use App\Models\Therapist;
use App\Models\TimeSlot;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class DatabaseDesignTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    /**
     * Test Roles and Permissions seeding and relationships.
     */
    public function test_roles_and_permissions_are_properly_configured(): void
    {
        $superAdminRole = Role::where('name', 'super_admin')->first();
        $this->assertNotNull($superAdminRole);
        $this->assertGreaterThan(10, $superAdminRole->permissions()->count());

        $this->assertDatabaseHas('roles', ['name' => 'super_admin']);
        $this->assertDatabaseHas('roles', ['name' => 'admin']);
        $this->assertDatabaseHas('roles', ['name' => 'staff']);

        $permission = Permission::where('name', 'bookings.view')->first();
        $this->assertNotNull($permission);
        $this->assertTrue($superAdminRole->hasPermission('bookings.view'));
    }

    /**
     * Test Admin model and Super Admin account.
     */
    public function test_super_admin_account_exists(): void
    {
        $admin = Admin::where('email', 'admin@wlu-clinic.local')->first();
        $this->assertNotNull($admin);
        $this->assertEquals('ผู้ดูแลระบบ', $admin->name);
        $this->assertTrue(Hash::check('Admin@1234', $admin->password));
        $this->assertTrue($admin->isSuperAdmin());
        $this->assertTrue($admin->hasPermission('any.permission'));
        $this->assertEquals('super_admin', $admin->role->name);
    }

    /**
     * Test Services (at least 8 services seeded).
     */
    public function test_services_seeded_correctly(): void
    {
        $this->assertGreaterThanOrEqual(8, Service::count());

        $this->assertDatabaseHas('services', ['code' => 'TTM-MASSAGE-60']);
        $this->assertDatabaseHas('services', ['code' => 'TTM-MASSAGE-90']);
        $this->assertDatabaseHas('services', ['code' => 'TTM-HERBAL-COMPRESS']);
        $this->assertDatabaseHas('services', ['code' => 'TTM-HERBAL-STEAM']);
        $this->assertDatabaseHas('services', ['code' => 'TTM-FOOT-MASSAGE']);
        $this->assertDatabaseHas('services', ['code' => 'TTM-FACIAL']);
        $this->assertDatabaseHas('services', ['code' => 'TTM-SALT-POT']);
        $this->assertDatabaseHas('services', ['code' => 'TTM-AUTO-ASSIGN']);
    }

    /**
     * Test Therapists (5 therapists seeded) and Service relationships.
     */
    public function test_therapists_seeded_with_services_and_schedules(): void
    {
        $this->assertGreaterThanOrEqual(5, Therapist::count());

        $therapist = Therapist::where('code', 'TH-001')->first();
        $this->assertNotNull($therapist);
        $this->assertGreaterThan(0, $therapist->services()->count());
        $this->assertEquals(7, $therapist->schedules()->count()); // 7 days of week
        $this->assertEquals('สมใจ สุขสวัสดิ์', $therapist->full_name);
        $this->assertStringContainsString('หมอใจ', $therapist->display_name);
    }

    /**
     * Test Clinic Schedule (Mon-Fri open, Sat-Sun closed).
     */
    public function test_clinic_schedules_mon_to_fri_open(): void
    {
        $this->assertEquals(7, ClinicSchedule::count());

        // Mon-Fri (1-5) must be open
        for ($day = 1; $day <= 5; $day++) {
            $schedule = ClinicSchedule::where('day_of_week', $day)->first();
            $this->assertNotNull($schedule);
            $this->assertTrue($schedule->is_open);
            $this->assertEquals('08:00:00', $schedule->open_time);
            $this->assertEquals('16:30:00', $schedule->close_time);
        }

        // Sat & Sun (6 & 0) must be closed
        $sun = ClinicSchedule::where('day_of_week', 0)->first();
        $sat = ClinicSchedule::where('day_of_week', 6)->first();
        $this->assertFalse($sun->is_open);
        $this->assertFalse($sat->is_open);
    }

    /**
     * Test Time Slots (9 slots 08:00 - 16:30).
     */
    public function test_time_slots_seeded_properly(): void
    {
        $this->assertEquals(9, TimeSlot::count());

        $firstSlot = TimeSlot::where('slot_number', 1)->first();
        $this->assertEquals('08:00:00', $firstSlot->start_time);

        $lastSlot = TimeSlot::where('slot_number', 9)->first();
        $this->assertEquals('16:30:00', $lastSlot->end_time);
    }

    /**
     * Test Settings helper and keys.
     */
    public function test_settings_seeded_and_helper_works(): void
    {
        $this->assertEquals(30, Setting::get('booking_advance_days'));
        $this->assertEquals(2, Setting::get('booking_close_before_hours'));
        $this->assertEquals(24, Setting::get('cancel_before_hours'));
        $this->assertEquals(24, Setting::get('reschedule_before_hours'));
        $this->assertEquals(3, Setting::get('max_active_bookings_per_customer'));
        $this->assertEquals('คลินิกแพทย์แผนไทย', Setting::get('clinic_name_th'));
        $this->assertEquals('Thai Traditional Medicine Clinic', Setting::get('clinic_name_en'));
        $this->assertEquals('โรงพยาบาลศูนย์การแพทย์ มหาวิทยาลัยวลัยลักษณ์', Setting::get('hospital_name'));
        $this->assertEquals(['email'], Setting::get('notification_channels'));
        $this->assertEquals(24, Setting::get('reminder_before_hours'));
    }

    /**
     * Test Booking model, relationship and booking number generation.
     */
    public function test_bookings_relationships_and_generation(): void
    {
        $bookingNo = Booking::generateBookingNo();
        $this->assertMatchesRegularExpression('/^WLU-\d{6}-\d{4}$/', $bookingNo);

        $booking = Booking::with(['customer', 'service', 'therapist', 'timeSlot', 'histories'])->first();
        $this->assertNotNull($booking);
        $this->assertNotNull($booking->customer);
        $this->assertNotNull($booking->service);
        $this->assertNotNull($booking->timeSlot);
        $this->assertGreaterThan(0, $booking->histories->count());
    }

    /**
     * Test AuditLog and BookingHistory recording.
     */
    public function test_audit_logs_and_histories(): void
    {
        $admin = Admin::first();
        $log = AuditLog::record($admin->id, 'test_action', $admin, ['test' => 1], ['test' => 2]);
        $this->assertNotNull($log);
        $this->assertEquals('test_action', $log->action);
        $this->assertEquals(['test' => 1], $log->old_values);
        $this->assertEquals(['test' => 2], $log->new_values);
        $this->assertEquals($admin->id, $log->admin->id);
    }
}

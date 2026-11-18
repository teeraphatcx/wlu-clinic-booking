<?php

namespace Database\Seeders;

use App\Models\ClinicHoliday;
use App\Models\ClinicSchedule;
use Illuminate\Database\Seeder;

class ClinicScheduleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 0=Sunday, 1=Monday, 2=Tuesday, 3=Wednesday, 4=Thursday, 5=Friday, 6=Saturday
        $schedules = [
            ['day_of_week' => 0, 'open_time' => '08:00:00', 'close_time' => '16:30:00', 'is_open' => false, 'max_bookings_per_slot' => 0],
            ['day_of_week' => 1, 'open_time' => '08:00:00', 'close_time' => '16:30:00', 'is_open' => true,  'max_bookings_per_slot' => 5],
            ['day_of_week' => 2, 'open_time' => '08:00:00', 'close_time' => '16:30:00', 'is_open' => true,  'max_bookings_per_slot' => 5],
            ['day_of_week' => 3, 'open_time' => '08:00:00', 'close_time' => '16:30:00', 'is_open' => true,  'max_bookings_per_slot' => 5],
            ['day_of_week' => 4, 'open_time' => '08:00:00', 'close_time' => '16:30:00', 'is_open' => true,  'max_bookings_per_slot' => 5],
            ['day_of_week' => 5, 'open_time' => '08:00:00', 'close_time' => '16:30:00', 'is_open' => true,  'max_bookings_per_slot' => 5],
            ['day_of_week' => 6, 'open_time' => '08:00:00', 'close_time' => '16:30:00', 'is_open' => false, 'max_bookings_per_slot' => 0],
        ];

        foreach ($schedules as $schedule) {
            ClinicSchedule::updateOrCreate(
                ['day_of_week' => $schedule['day_of_week']],
                $schedule
            );
        }

        // Example standard holidays
        $holidays = [
            ['date' => '2026-01-01', 'name' => 'วันขึ้นปีใหม่', 'is_full_day' => true, 'note' => 'วันหยุดราชการ'],
            ['date' => '2026-04-13', 'name' => 'วันสงกรานต์', 'is_full_day' => true, 'note' => 'วันหยุดสงกรานต์'],
            ['date' => '2026-04-14', 'name' => 'วันสงกรานต์', 'is_full_day' => true, 'note' => 'วันหยุดสงกรานต์'],
            ['date' => '2026-04-15', 'name' => 'วันสงกรานต์', 'is_full_day' => true, 'note' => 'วันหยุดสงกรานต์'],
            ['date' => '2026-05-01', 'name' => 'วันแรงงานแห่งชาติ', 'is_full_day' => true, 'note' => 'วันหยุดแรงงาน'],
            ['date' => '2026-12-05', 'name' => 'วันคล้ายวันพระบรมราชสมภพ ร.9 / วันพ่อแห่งชาติ', 'is_full_day' => true, 'note' => 'วันหยุดราชการ'],
            ['date' => '2026-12-31', 'name' => 'วันสิ้นปี', 'is_full_day' => true, 'note' => 'วันหยุดสิ้นปี'],
        ];

        foreach ($holidays as $holiday) {
            ClinicHoliday::updateOrCreate(
                ['date' => $holiday['date']],
                $holiday
            );
        }
    }
}

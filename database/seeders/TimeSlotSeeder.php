<?php

namespace Database\Seeders;

use App\Models\TimeSlot;
use Illuminate\Database\Seeder;

class TimeSlotSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $slots = [
            ['slot_number' => 1, 'start_time' => '08:00:00', 'end_time' => '09:00:00', 'label_th' => '08:00 - 09:00 น.', 'is_active' => true],
            ['slot_number' => 2, 'start_time' => '09:00:00', 'end_time' => '10:00:00', 'label_th' => '09:00 - 10:00 น.', 'is_active' => true],
            ['slot_number' => 3, 'start_time' => '10:00:00', 'end_time' => '11:00:00', 'label_th' => '10:00 - 11:00 น.', 'is_active' => true],
            ['slot_number' => 4, 'start_time' => '11:00:00', 'end_time' => '12:00:00', 'label_th' => '11:00 - 12:00 น.', 'is_active' => true],
            ['slot_number' => 5, 'start_time' => '12:00:00', 'end_time' => '13:00:00', 'label_th' => '12:00 - 13:00 น.', 'is_active' => true],
            ['slot_number' => 6, 'start_time' => '13:00:00', 'end_time' => '14:00:00', 'label_th' => '13:00 - 14:00 น.', 'is_active' => true],
            ['slot_number' => 7, 'start_time' => '14:00:00', 'end_time' => '15:00:00', 'label_th' => '14:00 - 15:00 น.', 'is_active' => true],
            ['slot_number' => 8, 'start_time' => '15:00:00', 'end_time' => '16:00:00', 'label_th' => '15:00 - 16:00 น.', 'is_active' => true],
            ['slot_number' => 9, 'start_time' => '16:00:00', 'end_time' => '16:30:00', 'label_th' => '16:00 - 16:30 น.', 'is_active' => true],
        ];

        foreach ($slots as $slot) {
            TimeSlot::updateOrCreate(
                ['slot_number' => $slot['slot_number']],
                $slot
            );
        }
    }
}

<?php

namespace Database\Seeders;

use App\Models\Service;
use App\Models\Therapist;
use App\Models\TherapistSchedule;
use Illuminate\Database\Seeder;

class TherapistSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $therapists = [
            [
                'code' => 'TH-001',
                'first_name' => 'สมใจ',
                'last_name' => 'สุขสวัสดิ์',
                'nickname' => 'หมอใจ',
                'phone' => '0812345671',
                'gender' => 'female',
                'bio' => 'แพทย์แผนไทยประยุกต์ มีประสบการณ์กว่า 8 ปี เชี่ยวชาญการนวดบำบัดอาการออฟฟิศซินโดรมและหัตถการประคบสมุนไพร',
                'start_date' => '2020-01-15',
                'is_active' => true,
                'services' => ['TTM-MASSAGE-60', 'TTM-MASSAGE-90', 'TTM-HERBAL-COMPRESS', 'TTM-OFFICE-SYNDROME', 'TTM-AUTO-ASSIGN'],
            ],
            [
                'code' => 'TH-002',
                'first_name' => 'มาลี',
                'last_name' => 'ดอกรัก',
                'nickname' => 'หมอลี',
                'phone' => '0812345672',
                'gender' => 'female',
                'bio' => 'ผู้ช่วยแพทย์แผนไทย ผ่านการอบรมนวดแผนไทย 372 ชั่วโมง ชำนาญการนวดกดจุดสะท้อนเท้าและนวดหน้าสมุนไพร',
                'start_date' => '2021-03-01',
                'is_active' => true,
                'services' => ['TTM-MASSAGE-60', 'TTM-MASSAGE-90', 'TTM-FOOT-MASSAGE', 'TTM-FACIAL', 'TTM-AUTO-ASSIGN'],
            ],
            [
                'code' => 'TH-003',
                'first_name' => 'กัญญา',
                'last_name' => 'วัฒนารมย์',
                'nickname' => 'หมอแอน',
                'phone' => '0812345673',
                'gender' => 'female',
                'bio' => 'แพทย์แผนไทย ชำนาญพิเศษด้านการดูแลมารดาหลังคลอด การทับหม้อเกลือ และการประคบสมุนไพรสด',
                'start_date' => '2019-06-10',
                'is_active' => true,
                'services' => ['TTM-MASSAGE-60', 'TTM-MASSAGE-90', 'TTM-SALT-POT', 'TTM-HERBAL-COMPRESS', 'TTM-AUTO-ASSIGN'],
            ],
            [
                'code' => 'TH-004',
                'first_name' => 'ประวิทย์',
                'last_name' => 'รักษาดี',
                'nickname' => 'หมอวิทย์',
                'phone' => '0812345674',
                'gender' => 'male',
                'bio' => 'แพทย์แผนไทย มีความเชี่ยวชาญการตรวจวินิจฉัยธาตุเจ้าเรือน นวดแก้อาการกล้ามเนื้อและกระดูก และนวดแผนไทย',
                'start_date' => '2018-11-01',
                'is_active' => true,
                'services' => ['TTM-MASSAGE-60', 'TTM-MASSAGE-90', 'TTM-OFFICE-SYNDROME', 'TTM-FOOT-MASSAGE', 'TTM-AUTO-ASSIGN'],
            ],
            [
                'code' => 'TH-005',
                'first_name' => 'ศิริพร',
                'last_name' => 'บุญช่วย',
                'nickname' => 'หมอพร',
                'phone' => '0812345675',
                'gender' => 'female',
                'bio' => 'ผู้เชี่ยวชาญศาสตร์การนวดเพื่อสุขภาพและการอบสมุนไพร ประสบการณ์ 5 ปี ดูแลผู้รับบริการด้วยความอ่อนโยน',
                'start_date' => '2022-02-15',
                'is_active' => true,
                'services' => ['TTM-MASSAGE-60', 'TTM-MASSAGE-90', 'TTM-HERBAL-STEAM', 'TTM-FACIAL', 'TTM-FOOT-MASSAGE', 'TTM-AUTO-ASSIGN'],
            ],
        ];

        foreach ($therapists as $tData) {
            $serviceCodes = $tData['services'];
            unset($tData['services']);

            $therapist = Therapist::updateOrCreate(
                ['code' => $tData['code']],
                $tData
            );

            // Map services
            $serviceIds = Service::whereIn('code', $serviceCodes)->pluck('id');
            $therapist->services()->sync($serviceIds);

            // Seed default weekly schedule for Monday to Friday (1-5)
            for ($day = 1; $day <= 5; $day++) {
                TherapistSchedule::updateOrCreate(
                    [
                        'therapist_id' => $therapist->id,
                        'day_of_week' => $day,
                    ],
                    [
                        'start_time' => '08:00:00',
                        'end_time' => '16:30:00',
                        'break_start' => '12:00:00',
                        'break_end' => '13:00:00',
                        'is_working' => true,
                    ]
                );
            }

            // Saturday & Sunday (6 and 0) are off
            foreach ([0, 6] as $offDay) {
                TherapistSchedule::updateOrCreate(
                    [
                        'therapist_id' => $therapist->id,
                        'day_of_week' => $offDay,
                    ],
                    [
                        'start_time' => '08:00:00',
                        'end_time' => '16:30:00',
                        'break_start' => null,
                        'break_end' => null,
                        'is_working' => false,
                    ]
                );
            }
        }
    }
}

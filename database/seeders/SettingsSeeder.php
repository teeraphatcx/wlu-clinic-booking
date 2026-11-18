<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $settings = [
            // General Settings
            [
                'key' => 'clinic_name_th',
                'value' => 'คลินิกแพทย์แผนไทย',
                'type' => 'string',
                'group' => 'general',
                'description' => 'ชื่อคลินิกภาษาไทย',
                'is_public' => true,
            ],
            [
                'key' => 'clinic_name_en',
                'value' => 'Thai Traditional Medicine Clinic',
                'type' => 'string',
                'group' => 'general',
                'description' => 'ชื่อคลินิกภาษาอังกฤษ',
                'is_public' => true,
            ],
            [
                'key' => 'hospital_name',
                'value' => 'โรงพยาบาลศูนย์การแพทย์ มหาวิทยาลัยวลัยลักษณ์',
                'type' => 'string',
                'group' => 'general',
                'description' => 'ชื่อโรงพยาบาล/สังกัด',
                'is_public' => true,
            ],
            [
                'key' => 'clinic_phone',
                'value' => '0-7567-3000',
                'type' => 'string',
                'group' => 'general',
                'description' => 'เบอร์โทรศัพท์ติดต่อคลินิก',
                'is_public' => true,
            ],
            [
                'key' => 'clinic_address',
                'value' => '222 ต.ไทยบุรี อ.ท่าศาลา จ.นครศรีธรรมราช 80160',
                'type' => 'string',
                'group' => 'general',
                'description' => 'ที่อยู่คลินิก',
                'is_public' => true,
            ],
            [
                'key' => 'site_logo',
                'value' => '',
                'type' => 'string',
                'group' => 'website',
                'description' => 'โลโก้ที่แสดงบนเว็บไซต์และหลังบ้าน',
                'is_public' => true,
            ],
            [
                'key' => 'site_header_image',
                'value' => '',
                'type' => 'string',
                'group' => 'website',
                'description' => 'ภาพ Header แบบยาวของเว็บไซต์',
                'is_public' => true,
            ],
            [
                'key' => 'site_header_link',
                'value' => '',
                'type' => 'string',
                'group' => 'website',
                'description' => 'ลิงก์เมื่อกดภาพ Header แบบยาว',
                'is_public' => true,
            ],

            // Booking Settings
            [
                'key' => 'booking_advance_days',
                'value' => '30',
                'type' => 'integer',
                'group' => 'booking',
                'description' => 'จองล่วงหน้าได้ไม่เกินกี่วัน',
                'is_public' => true,
            ],
            [
                'key' => 'booking_close_before_hours',
                'value' => '2',
                'type' => 'integer',
                'group' => 'booking',
                'description' => 'ปิดรับจองก่อนเวลาเข้ารับบริการกี่ชั่วโมง',
                'is_public' => true,
            ],
            [
                'key' => 'cancel_before_hours',
                'value' => '24',
                'type' => 'integer',
                'group' => 'booking',
                'description' => 'ยกเลิกการจองล่วงหน้าได้ก่อนกี่ชั่วโมง',
                'is_public' => true,
            ],
            [
                'key' => 'reschedule_before_hours',
                'value' => '24',
                'type' => 'integer',
                'group' => 'booking',
                'description' => 'เลื่อนการจองล่วงหน้าได้ก่อนกี่ชั่วโมง',
                'is_public' => true,
            ],
            [
                'key' => 'max_active_bookings_per_customer',
                'value' => '3',
                'type' => 'integer',
                'group' => 'booking',
                'description' => 'จำนวนคิวที่รอดำเนินการสูงสุดต่อผู้รับบริการ 1 ท่าน',
                'is_public' => true,
            ],
            [
                'key' => 'default_slot_capacity',
                'value' => '5',
                'type' => 'integer',
                'group' => 'booking',
                'description' => 'จำนวนผู้รับบริการสูงสุดต่อรอบเวลา',
                'is_public' => false,
            ],

            // Notification Settings
            [
                'key' => 'notification_channels',
                'value' => json_encode(['email']),
                'type' => 'json',
                'group' => 'notification',
                'description' => 'ช่องทางการแจ้งเตือนที่เปิดใช้งาน (email, sms, line)',
                'is_public' => false,
            ],
            [
                'key' => 'reminder_before_hours',
                'value' => '24',
                'type' => 'integer',
                'group' => 'notification',
                'description' => 'ส่งการแจ้งเตือนเตือนความจำก่อนนัดหมายกี่ชั่วโมง',
                'is_public' => false,
            ],

            // Website / Policy Settings
            [
                'key' => 'pdpa_consent_text',
                'value' => 'ข้าพเจ้ายินยอมให้โรงพยาบาลศูนย์การแพทย์ มหาวิทยาลัยวลัยลักษณ์ เก็บรวบรวม ใช้ และเปิดเผยข้อมูลส่วนบุคคลเพื่อประโยชน์ในการให้บริการจองคิวและการรักษาพยาบาลตาม พ.ร.บ. คุ้มครองข้อมูลส่วนบุคคล พ.ศ. 2562',
                'type' => 'string',
                'group' => 'website',
                'description' => 'ข้อความขอความยินยอม PDPA สำหรับผู้รับบริการ',
                'is_public' => true,
            ],
            [
                'key' => 'cancellation_policy',
                'value' => 'หากต้องการยกเลิกหรือเลื่อนนัดหมาย กรุณาแจ้งล่วงหน้าอย่างน้อย 24 ชั่วโมงก่อนถึงเวลานัดหมาย',
                'type' => 'string',
                'group' => 'website',
                'description' => 'นโยบายการยกเลิกนัดหมาย',
                'is_public' => true,
            ],
        ];

        foreach ($settings as $settingData) {
            Setting::updateOrCreate(
                ['key' => $settingData['key']],
                $settingData
            );
        }
    }
}

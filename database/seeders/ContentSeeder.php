<?php

namespace Database\Seeders;

use App\Models\Content;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class ContentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $contents = [
            [
                'type' => 'announcement',
                'title' => 'ยินดีต้อนรับสู่ระบบจองคิวออนไลน์ คลินิกแพทย์แผนไทย ศูนย์การแพทย์ ม.วลัยลักษณ์',
                'slug' => 'welcome-to-ttm-clinic',
                'content' => 'คลินิกแพทย์แผนไทย โรงพยาบาลศูนย์การแพทย์ มหาวิทยาลัยวลัยลักษณ์ เปิดให้บริการตรวจวินิจฉัย บำบัดรักษา และฟื้นฟูสุขภาพด้วยศาสตร์การแพทย์แผนไทย ทุกวันจันทร์ - วันศุกร์ เวลา 08:00 - 16:30 น. (หยุดวันเสาร์-อาทิตย์ และวันหยุดนักขัตฤกษ์)',
                'excerpt' => 'เปิดให้บริการตรวจวินิจฉัย บำบัดรักษา และฟื้นฟูสุขภาพด้วยศาสตร์การแพทย์แผนไทย ทุกวันจันทร์ - วันศุกร์',
                'image' => null,
                'is_active' => true,
                'sort_order' => 1,
                'published_at' => Carbon::now(),
            ],
            [
                'type' => 'faq',
                'title' => 'ต้องเตรียมตัวอย่างไรก่อนเข้ารับบริการนวดแผนไทย?',
                'slug' => 'how-to-prepare-for-massage',
                'content' => '1. ควรสวมเสื้อผ้าที่หลวม สวมใส่สบาย ไม่รัดแน่น'."\n".
                             '2. หลีกเลี่ยงการรับประทานอาหารมื้อหนักก่อนนวดอย่างน้อย 1 ชั่วโมง'."\n".
                             '3. แจ้งประวัติสุขภาพ โรคประจำตัว เช่น ความดันโลหิตสูง โรคหัวใจ การผ่าตัด หรือการตั้งครรภ์ให้แพทย์แผนไทยทราบก่อนทุกครั้ง',
                'excerpt' => 'คำแนะนำการเตรียมตัวก่อนเข้ารับบริการนวดแผนไทย',
                'image' => null,
                'is_active' => true,
                'sort_order' => 1,
                'published_at' => Carbon::now(),
            ],
            [
                'type' => 'faq',
                'title' => 'สามารถยกเลิกหรือเลื่อนนัดหมายได้อย่างไร?',
                'slug' => 'how-to-cancel-or-reschedule',
                'content' => 'ผู้รับบริการสามารถยกเลิกหรือเปลี่ยนแปลงเวลาการจองได้ด้วยตนเองผ่านระบบ หรือติดต่อเจ้าหน้าที่ล่วงหน้าอย่างน้อย 24 ชั่วโมงก่อนเวลานัดหมาย',
                'excerpt' => 'เงื่อนไขและวิธียกเลิกหรือเลื่อนนัดหมาย',
                'image' => null,
                'is_active' => true,
                'sort_order' => 2,
                'published_at' => Carbon::now(),
            ],
        ];

        foreach ($contents as $c) {
            Content::updateOrCreate(
                ['slug' => $c['slug']],
                $c
            );
        }
    }
}

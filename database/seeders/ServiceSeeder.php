<?php

namespace Database\Seeders;

use App\Models\Service;
use Illuminate\Database\Seeder;

class ServiceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $services = [
            [
                'code' => 'TTM-MASSAGE-60',
                'name_th' => 'นวดแผนไทยเพื่อสุขภาพ (60 นาที)',
                'name_en' => 'Traditional Thai Massage (60 mins)',
                'description' => 'การนวดเพื่อส่งเสริมสุขภาพ คลายกล้ามเนื้อ กระตุ้นการไหลเวียนโลหิต และบรรเทาอาการเมื่อยล้า',
                'price' => 300.00,
                'duration_minutes' => 60,
                'requires_therapist_selection' => true,
                'image' => null,
                'sort_order' => 1,
                'is_active' => true,
            ],
            [
                'code' => 'TTM-MASSAGE-90',
                'name_th' => 'นวดแผนไทยเพื่อสุขภาพ (90 นาที)',
                'name_en' => 'Traditional Thai Massage (90 mins)',
                'description' => 'การนวดแผนไทยแบบครบวงจร ทั่วร่างกาย คลายเส้น ผ่อนคลายกล้ามเนื้อลึก',
                'price' => 450.00,
                'duration_minutes' => 90,
                'requires_therapist_selection' => true,
                'image' => null,
                'sort_order' => 2,
                'is_active' => true,
            ],
            [
                'code' => 'TTM-HERBAL-COMPRESS',
                'name_th' => 'การประคบสมุนไพรสด',
                'name_en' => 'Herbal Compress Therapy',
                'description' => 'การใช้ลูกประคบสมุนไพรสดอุ่นประคบบริเวณที่มีอาการปวดเมื่อย ช่วยคลายเส้นและลดการอักเสบ',
                'price' => 250.00,
                'duration_minutes' => 45,
                'requires_therapist_selection' => true,
                'image' => null,
                'sort_order' => 3,
                'is_active' => true,
            ],
            [
                'code' => 'TTM-HERBAL-STEAM',
                'name_th' => 'การอบไอน้ำสมุนไพร',
                'name_en' => 'Herbal Steam Sauna',
                'description' => 'การอบสมุนไพรในตู้อบ ช่วยขับเหงื่อ ขับสารพิษ บำรุงผิวพรรณ และช่วยให้ระบบทางเดินหายใจโล่งสบาย',
                'price' => 200.00,
                'duration_minutes' => 30,
                'requires_therapist_selection' => false,
                'image' => null,
                'sort_order' => 4,
                'is_active' => true,
            ],
            [
                'code' => 'TTM-FOOT-MASSAGE',
                'name_th' => 'การนวดกดจุดสะท้อนเท้า',
                'name_en' => 'Foot Reflexology Massage',
                'description' => 'การนวดและกดจุดสะท้อนบนฝ่าเท้าเพื่อกระตุ้นการทำงานของอวัยวะต่างๆ ภายในร่างกาย',
                'price' => 250.00,
                'duration_minutes' => 60,
                'requires_therapist_selection' => true,
                'image' => null,
                'sort_order' => 5,
                'is_active' => true,
            ],
            [
                'code' => 'TTM-FACIAL',
                'name_th' => 'การนวดและพอกหน้าสมุนไพร',
                'name_en' => 'Herbal Facial Massage & Treatment',
                'description' => 'การนวดกระตุ้นการไหลเวียนใบหน้า พร้อมพอกสมุนไพรบำรุงผิวให้เปล่งปลั่งอ่อนเยาว์',
                'price' => 350.00,
                'duration_minutes' => 60,
                'requires_therapist_selection' => true,
                'image' => null,
                'sort_order' => 6,
                'is_active' => true,
            ],
            [
                'code' => 'TTM-SALT-POT',
                'name_th' => 'การทับหม้อเกลือฟื้นฟูมารดาหลังคลอด',
                'name_en' => 'Salt Pot Therapy (Postpartum Care)',
                'description' => 'การใช้หม้อเกลือสมุนไพรอุ่นประคบหน้าท้อง เพื่อช่วยขับน้ำคาวปลา มดลูกเข้าอู่เร็ว และกระชับหน้าท้อง',
                'price' => 500.00,
                'duration_minutes' => 90,
                'requires_therapist_selection' => true,
                'image' => null,
                'sort_order' => 7,
                'is_active' => true,
            ],
            [
                'code' => 'TTM-AUTO-ASSIGN',
                'name_th' => 'บริการตรวจประเมิน (เจ้าหน้าที่จัดหมอให้อัตโนมัติ)',
                'name_en' => 'Consultation & Auto Therapist Assignment',
                'description' => 'ผู้รับบริการประสงค์ให้คลินิกและแพทย์แผนไทยตรวจประเมินอาการ และจัดสรรผู้ให้บริการตามความเหมาะสม',
                'price' => 300.00,
                'duration_minutes' => 60,
                'requires_therapist_selection' => false,
                'image' => null,
                'sort_order' => 8,
                'is_active' => true,
            ],
            [
                'code' => 'TTM-OFFICE-SYNDROME',
                'name_th' => 'นวดรักษาอาการออฟฟิศซินโดรม',
                'name_en' => 'Office Syndrome Therapeutic Massage',
                'description' => 'การนวดบำบัดรักษาเน้นเฉพาะจุด คอ บ่า ไหล่ และสะบัก สำหรับผู้ที่มีอาการกล้ามเนื้อเกร็งตัวเรื้อรัง',
                'price' => 400.00,
                'duration_minutes' => 60,
                'requires_therapist_selection' => true,
                'image' => null,
                'sort_order' => 9,
                'is_active' => true,
            ],
        ];

        foreach ($services as $serviceData) {
            Service::updateOrCreate(
                ['code' => $serviceData['code']],
                $serviceData
            );
        }
    }
}

@extends('layouts.app')

@section('title', 'นโยบายความเป็นส่วนตัว')

@section('content')
<div class="max-w-3xl mx-auto px-4 py-8">
    <h1 class="text-2xl font-bold text-brand-800 mb-2">นโยบายความเป็นส่วนตัว</h1>
    <p class="text-sm text-gray-500 mb-8">อัปเดตล่าสุด: มกราคม 2567</p>

    <div class="bg-blue-50 border border-blue-100 rounded-xl p-5 mb-8">
        <p class="text-blue-800 text-sm">
            <strong>คลินิกแพทย์แผนไทย โรงพยาบาลศูนย์การแพทย์ มหาวิทยาลัยวลัยลักษณ์</strong>
            ให้ความสำคัญกับการปกป้องข้อมูลส่วนบุคคลของท่าน และปฏิบัติตามพระราชบัญญัติคุ้มครองข้อมูลส่วนบุคคล พ.ศ. 2562 (PDPA)
        </p>
    </div>

    <div class="prose prose-sm max-w-none space-y-8">
        <section>
            <h2 class="text-lg font-bold text-gray-900 mb-3">1. ข้อมูลที่เราเก็บรวบรวม</h2>
            <div class="text-gray-700 space-y-2">
                <p>เราเก็บรวบรวมข้อมูลส่วนบุคคลดังต่อไปนี้เพื่อให้บริการระบบจองคิว:</p>
                <ul class="list-disc pl-5 space-y-1">
                    <li><strong>ชื่อ-นามสกุล:</strong> เพื่อระบุตัวตนและยืนยันการนัดหมาย</li>
                    <li><strong>หมายเลขโทรศัพท์:</strong> เพื่อการยืนยันการจองและการแจ้งเตือน</li>
                    <li><strong>อีเมล (ไม่บังคับ):</strong> เพื่อรับการแจ้งเตือนทางอีเมล</li>
                    <li><strong>ข้อมูลสุขภาพ (จากหมายเหตุที่กรอก):</strong> เพื่อการให้บริการที่เหมาะสม</li>
                    <li><strong>ข้อมูลการจอง:</strong> บริการ วันเวลา และประวัติการนัดหมาย</li>
                    <li><strong>หมายเลข IP:</strong> เพื่อความปลอดภัยของระบบ</li>
                </ul>
            </div>
        </section>

        <section>
            <h2 class="text-lg font-bold text-gray-900 mb-3">2. วัตถุประสงค์การใช้ข้อมูล</h2>
            <div class="text-gray-700 space-y-2">
                <ul class="list-disc pl-5 space-y-1">
                    <li>จัดการและยืนยันการจองคิวรับบริการ</li>
                    <li>ส่งการแจ้งเตือนและการเตือนความจำการนัดหมาย</li>
                    <li>ปรับปรุงคุณภาพการให้บริการ</li>
                    <li>จัดทำสถิติและรายงานภายในองค์กร (ข้อมูลสรุปรวม ไม่ระบุตัวตน)</li>
                    <li>ปฏิบัติตามกฎหมายและระเบียบที่เกี่ยวข้อง</li>
                </ul>
            </div>
        </section>

        <section>
            <h2 class="text-lg font-bold text-gray-900 mb-3">3. การเปิดเผยข้อมูล</h2>
            <p class="text-gray-700">เราไม่เปิดเผยข้อมูลส่วนบุคคลของท่านต่อบุคคลภายนอก ยกเว้น:</p>
            <ul class="list-disc pl-5 mt-2 space-y-1 text-gray-700">
                <li>บุคลากรทางการแพทย์ที่เกี่ยวข้องกับการดูแลรักษาของท่านโดยตรง</li>
                <li>เมื่อกฎหมายกำหนดให้ต้องเปิดเผย</li>
                <li>เพื่อปกป้องสิทธิ์และความปลอดภัยของท่านหรือผู้อื่น</li>
            </ul>
        </section>

        <section>
            <h2 class="text-lg font-bold text-gray-900 mb-3">4. ระยะเวลาการเก็บข้อมูล</h2>
            <p class="text-gray-700">เราเก็บรักษาข้อมูลส่วนบุคคลของท่านตลอดระยะเวลาที่จำเป็นสำหรับวัตถุประสงค์ที่ระบุไว้ และตามที่กฎหมายกำหนด โดยทั่วไปไม่เกิน 10 ปีนับจากวันสุดท้ายที่ใช้บริการ</p>
        </section>

        <section>
            <h2 class="text-lg font-bold text-gray-900 mb-3">5. สิทธิ์ของท่านในฐานะเจ้าของข้อมูล</h2>
            <div class="text-gray-700">
                <p class="mb-2">ท่านมีสิทธิ์ดังต่อไปนี้ตาม PDPA:</p>
                <ul class="list-disc pl-5 space-y-1">
                    <li><strong>สิทธิ์เข้าถึง:</strong> ขอดูข้อมูลส่วนบุคคลของตนเอง</li>
                    <li><strong>สิทธิ์แก้ไข:</strong> ขอแก้ไขข้อมูลที่ไม่ถูกต้อง</li>
                    <li><strong>สิทธิ์ลบ:</strong> ขอลบข้อมูล (เมื่อไม่มีเหตุผลทางกฎหมายให้เก็บต่อ)</li>
                    <li><strong>สิทธิ์คัดค้าน:</strong> คัดค้านการประมวลผลในบางกรณี</li>
                    <li><strong>สิทธิ์ถ่ายโอน:</strong> ขอรับสำเนาข้อมูลในรูปแบบที่อ่านได้</li>
                    <li><strong>สิทธิ์ถอนความยินยอม:</strong> ถอนความยินยอมได้ตลอดเวลา</li>
                </ul>
            </div>
        </section>

        <section>
            <h2 class="text-lg font-bold text-gray-900 mb-3">6. การรักษาความปลอดภัย</h2>
            <p class="text-gray-700">เราใช้มาตรการรักษาความปลอดภัยที่เหมาะสม รวมถึงการเข้ารหัสข้อมูล การควบคุมการเข้าถึง และการตรวจสอบความปลอดภัยอย่างสม่ำเสมอ เพื่อปกป้องข้อมูลส่วนบุคคลของท่าน</p>
        </section>

        <section>
            <h2 class="text-lg font-bold text-gray-900 mb-3">7. ติดต่อเรา</h2>
            <div class="bg-gray-50 rounded-xl p-4 text-gray-700">
                <p><strong>เจ้าหน้าที่คุ้มครองข้อมูลส่วนบุคคล (DPO)</strong></p>
                <p class="mt-1">คลินิกแพทย์แผนไทย โรงพยาบาลศูนย์การแพทย์ มหาวิทยาลัยวลัยลักษณ์</p>
                <p>เลขที่ 222 ตำบลไทยบุรี อำเภอท่าศาลา จังหวัดนครศรีธรรมราช 80160</p>
                <p>โทร: 075-673-000</p>
                <p>อีเมล: thaimedclinic@wu.ac.th</p>
            </div>
        </section>
    </div>
</div>
@endsection

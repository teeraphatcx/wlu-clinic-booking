@extends('admin.layout')

@section('title', 'ตั้งค่าเว็บไซต์และระบบ')

@section('content')
<div class="mb-6 flex flex-wrap items-end justify-between gap-4">
    <div>
        <p class="mb-1 text-xs font-semibold uppercase tracking-wider text-brand-600">System Settings</p>
        <h1 class="text-2xl font-bold text-slate-800">ตั้งค่าเว็บไซต์และระบบ</h1>
        <p class="text-sm text-slate-500">จัดการข้อมูลคลินิก ภาพลักษณ์ โลโก้ กฎเกณฑ์การจองคิว การแจ้งเตือน และข้อกำหนดนโยบาย</p>
    </div>
    <a href="{{ route('home') }}" target="_blank" class="inline-flex items-center gap-1.5 rounded-xl border border-slate-200 bg-white px-3.5 py-2 text-xs font-semibold text-slate-700 hover:bg-slate-50 transition shadow-sm">
        <svg class="h-4 w-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
        ดูหน้าเว็บไซต์จริง
    </a>
</div>

<div class="grid gap-6 lg:grid-cols-3">
    {{-- ฝั่งซ้าย: โลโก้ และ ภาพ Header --}}
    <div class="space-y-6 lg:col-span-1">
        {{-- โลโก้เว็บไซต์ --}}
        <section class="rounded-2xl border border-slate-100 bg-white p-6 shadow-sm">
            <div class="border-b border-slate-100 pb-4">
                <h2 class="text-base font-bold text-slate-800 flex items-center gap-2">
                    <svg class="h-5 w-5 text-brand-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    โลโก้คลินิก (Site Logo)
                </h2>
                <p class="mt-1 text-xs text-slate-500">รองรับ PNG, JPG, WEBP หรือ SVG (ขนาดไม่เกิน 2 MB)</p>
            </div>

            <div class="mt-5 flex min-h-36 items-center justify-center rounded-2xl border border-dashed border-brand-200 bg-brand-50/50 p-6">
                @if($siteLogo)
                    <div class="text-center">
                        <img src="{{ asset('storage/'.$siteLogo) }}" alt="โลโก้เว็บไซต์ปัจจุบัน" class="mx-auto max-h-24 max-w-full object-contain drop-shadow-sm">
                        <span class="mt-2 inline-block rounded-full bg-emerald-50 px-2.5 py-0.5 text-[11px] font-semibold text-emerald-700 border border-emerald-100">กำลังใช้งาน</span>
                    </div>
                @else
                    <div class="text-center">
                        <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-2xl bg-gradient-to-br from-brand-700 to-brand-900 text-xl font-bold text-white shadow-md">มวล.</div>
                        <p class="mt-2 text-xs text-slate-400">ใช้สัญลักษณ์เริ่มต้นของระบบ</p>
                    </div>
                @endif
            </div>

            <form method="post" action="{{ route('admin.settings.logo') }}" enctype="multipart/form-data" class="mt-5 space-y-3">
                @csrf
                <div>
                    <label for="logo" class="mb-1.5 block text-xs font-semibold text-slate-700">อัปโหลดไฟล์โลโก้ใหม่</label>
                    <input id="logo" type="file" name="logo" accept=".png,.jpg,.jpeg,.webp,.svg,image/png,image/jpeg,image/webp,image/svg+xml" required class="block w-full rounded-xl border border-slate-200 bg-slate-50 text-xs text-slate-600 file:mr-3 file:rounded-lg file:border-0 file:bg-brand-100 file:px-3 file:py-2 file:text-xs file:font-semibold file:text-brand-800 hover:file:bg-brand-200 transition">
                </div>
                <button type="submit" class="w-full rounded-xl bg-brand-700 px-4 py-2.5 text-xs font-semibold text-white shadow-sm hover:bg-brand-800 transition">
                    บันทึกโลโก้
                </button>
            </form>

            @if($siteLogo)
                <form method="post" action="{{ route('admin.settings.logo.remove') }}" class="mt-3 text-center" onsubmit="return confirm('ยืนยันการลบโลโก้ปัจจุบันและกลับไปใช้สัญลักษณ์เริ่มต้นหรือไม่?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="text-xs font-semibold text-rose-600 hover:text-rose-800 hover:underline">
                        ลบโลโก้และใช้ค่าเริ่มต้น
                    </button>
                </form>
            @endif
        </section>

        {{-- ภาพ Header แบนเนอร์ --}}
        <section class="rounded-2xl border border-slate-100 bg-white p-6 shadow-sm">
            <div class="border-b border-slate-100 pb-4">
                <h2 class="text-base font-bold text-slate-800 flex items-center gap-2">
                    <svg class="h-5 w-5 text-brand-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/></svg>
                    ภาพ Header แบบยาว
                </h2>
                <p class="mt-1 text-xs text-slate-500">ภาพแบนเนอร์กว้างใต้แถบเมนู (แนะนำ 1600 × 360 px ขนาดไม่เกิน 4 MB)</p>
            </div>

            @php
                $siteHeader = \App\Models\Setting::get('site_header_image');
                $siteHeaderLink = \App\Models\Setting::get('site_header_link');
            @endphp

            <div class="mt-5 overflow-hidden rounded-2xl border border-dashed border-brand-200 bg-brand-50/50">
                @if($siteHeader)
                    <img src="{{ asset('storage/'.$siteHeader) }}" alt="ภาพ Header ปัจจุบัน" class="max-h-40 w-full object-cover">
                @else
                    <div class="flex h-28 items-center justify-center text-xs text-brand-600 font-medium">ยังไม่ได้ตั้งค่าภาพ Header</div>
                @endif
            </div>

            <form method="post" action="{{ route('admin.settings.header') }}" enctype="multipart/form-data" class="mt-5 space-y-3">
                @csrf
                <div>
                    <label for="header_image" class="mb-1.5 block text-xs font-semibold text-slate-700">เลือกภาพ Header ใหม่</label>
                    <input id="header_image" type="file" name="header_image" accept=".png,.jpg,.jpeg,.webp,image/png,image/jpeg,image/webp" required class="block w-full rounded-xl border border-slate-200 bg-slate-50 text-xs text-slate-600 file:mr-3 file:rounded-lg file:border-0 file:bg-brand-100 file:px-3 file:py-2 file:text-xs file:font-semibold file:text-brand-800 hover:file:bg-brand-200 transition">
                </div>
                <button type="submit" class="w-full rounded-xl bg-brand-700 px-4 py-2.5 text-xs font-semibold text-white shadow-sm hover:bg-brand-800 transition">
                    อัปโหลดภาพ Header
                </button>
            </form>

            <form method="post" action="{{ route('admin.settings.header.link') }}" class="mt-5 border-t border-slate-100 pt-4 space-y-3">
                @csrf
                <div>
                    <label for="header_link" class="mb-1.5 block text-xs font-semibold text-slate-700">ลิงก์ปลายทางเมื่อคลิกภาพ</label>
                    <input id="header_link" type="text" name="header_link" value="{{ old('header_link', $siteHeaderLink) }}" placeholder="เช่น /booking หรือ https://..." class="w-full rounded-xl border-slate-300 text-xs focus:border-brand-500 focus:ring-brand-200">
                    <p class="mt-1 text-[11px] text-slate-400">เว้นว่างได้หากไม่ต้องการให้คลิกเปิดลิงก์</p>
                </div>
                <button type="submit" class="w-full rounded-xl border border-brand-300 bg-brand-50 px-4 py-2 text-xs font-semibold text-brand-800 hover:bg-brand-100 transition">
                    บันทึกลิงก์แบนเนอร์
                </button>
            </form>

            @if($siteHeader)
                <form method="post" action="{{ route('admin.settings.header.remove') }}" class="mt-3 text-center" onsubmit="return confirm('ยืนยันการลบภาพ Header ปัจจุบันหรือไม่?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="text-xs font-semibold text-rose-600 hover:text-rose-800 hover:underline">
                        ลบภาพ Header
                    </button>
                </form>
            @endif
        </section>
    </div>

    {{-- ฝั่งขวา: ข้อมูลทั่วไป, กฎเกณฑ์การจอง, การแจ้งเตือน, PDPA --}}
    <div class="space-y-6 lg:col-span-2">
        {{-- ข้อมูลทั่วไปและช่องทางติดต่อ --}}
        <section class="rounded-2xl border border-slate-100 bg-white p-6 shadow-sm">
            <div class="border-b border-slate-100 pb-4 mb-5">
                <h2 class="text-lg font-bold text-slate-800 flex items-center gap-2">
                    <svg class="h-5 w-5 text-brand-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                    ข้อมูลทั่วไปและช่องทางติดต่อคลินิก
                </h2>
                <p class="mt-0.5 text-xs text-slate-500">ข้อมูลนี้จะแสดงในส่วนหัว ส่วนท้าย และหน้าติดต่อบนเว็บไซต์</p>
            </div>

            <form method="post" action="{{ route('admin.settings.general') }}" class="grid gap-4 sm:grid-cols-2">
                @csrf
                @method('PUT')
                <div>
                    <label class="mb-1 block text-xs font-semibold text-slate-700">ชื่อคลินิกภาษาไทย <span class="text-rose-500">*</span></label>
                    <input name="clinic_name_th" required value="{{ old('clinic_name_th', $settings->get('clinic_name_th')?->value ?? 'คลินิกแพทย์แผนไทย') }}" class="w-full rounded-xl border-slate-300 text-sm focus:border-brand-500 focus:ring-brand-200">
                </div>
                <div>
                    <label class="mb-1 block text-xs font-semibold text-slate-700">ชื่อคลินิกภาษาอังกฤษ</label>
                    <input name="clinic_name_en" value="{{ old('clinic_name_en', $settings->get('clinic_name_en')?->value) }}" class="w-full rounded-xl border-slate-300 text-sm focus:border-brand-500 focus:ring-brand-200">
                </div>
                <div>
                    <label class="mb-1 block text-xs font-semibold text-slate-700">โรงพยาบาล/หน่วยงานสังกัด <span class="text-rose-500">*</span></label>
                    <input name="hospital_name" required value="{{ old('hospital_name', $settings->get('hospital_name')?->value) }}" class="w-full rounded-xl border-slate-300 text-sm focus:border-brand-500 focus:ring-brand-200">
                </div>
                <div>
                    <label class="mb-1 block text-xs font-semibold text-slate-700">เบอร์โทรศัพท์ติดต่อ <span class="text-rose-500">*</span></label>
                    <input name="clinic_phone" required value="{{ old('clinic_phone', $settings->get('clinic_phone')?->value) }}" class="w-full rounded-xl border-slate-300 text-sm focus:border-brand-500 focus:ring-brand-200">
                </div>
                <div>
                    <label class="mb-1 block text-xs font-semibold text-slate-700">อีเมลติดต่อ</label>
                    <input name="clinic_email" type="email" value="{{ old('clinic_email', $settings->get('clinic_email')?->value) }}" class="w-full rounded-xl border-slate-300 text-sm focus:border-brand-500 focus:ring-brand-200">
                </div>
                <div>
                    <label class="mb-1 block text-xs font-semibold text-slate-700">ลิงก์ Google Maps</label>
                    <input name="clinic_map_url" type="url" value="{{ old('clinic_map_url', $settings->get('clinic_map_url')?->value) }}" placeholder="https://maps.google.com/..." class="w-full rounded-xl border-slate-300 text-sm focus:border-brand-500 focus:ring-brand-200">
                </div>
                <div class="sm:col-span-2">
                    <label class="mb-1 block text-xs font-semibold text-slate-700">ที่อยู่คลินิก <span class="text-rose-500">*</span></label>
                    <textarea name="clinic_address" required rows="2" class="w-full rounded-xl border-slate-300 text-sm focus:border-brand-500 focus:ring-brand-200">{{ old('clinic_address', $settings->get('clinic_address')?->value) }}</textarea>
                </div>
                <div class="sm:col-span-2">
                    <label class="mb-1 block text-xs font-semibold text-slate-700">คำอธิบายภาพรวมคลินิก</label>
                    <textarea name="clinic_description" rows="3" class="w-full rounded-xl border-slate-300 text-sm focus:border-brand-500 focus:ring-brand-200">{{ old('clinic_description', $settings->get('clinic_description')?->value) }}</textarea>
                </div>
                <div class="sm:col-span-2 flex justify-end pt-2">
                    <button type="submit" class="inline-flex items-center gap-2 rounded-xl bg-brand-700 px-5 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-brand-800 transition">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        บันทึกข้อมูลทั่วไป
                    </button>
                </div>
            </form>
        </section>

        {{-- เงื่อนไขและกฎเกณฑ์การจองคิว --}}
        <section class="rounded-2xl border border-slate-100 bg-white p-6 shadow-sm">
            <div class="border-b border-slate-100 pb-4 mb-5">
                <h2 class="text-lg font-bold text-slate-800 flex items-center gap-2">
                    <svg class="h-5 w-5 text-brand-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    เงื่อนไขและข้อจำกัดการจองคิว
                </h2>
                <p class="mt-0.5 text-xs text-slate-500">กำหนดระยะเวลาการจองล่วงหน้า การยกเลิก และโควตาการจองของผู้รับบริการ</p>
            </div>

            <form method="post" action="{{ route('admin.settings.booking') }}" class="grid gap-4 sm:grid-cols-2">
                @csrf
                @method('PUT')

                @php
                    $bookingFields = [
                        'booking_advance_days' => ['label' => 'เปิดจองล่วงหน้าสูงสุด (วัน)', 'desc' => 'ระยะเวลาที่ผู้รับบริการสามารถเลือกวันจองได้ล่วงหน้า', 'min' => 1, 'max' => 365, 'default' => 30],
                        'booking_close_before_hours' => ['label' => 'ปิดรับจองก่อนเวลาบริการ (ชั่วโมง)', 'desc' => 'ระยะเวลาตัดรอบก่อนเริ่มรอบเวลา เช่น 2 ชม.', 'min' => 0, 'max' => 720, 'default' => 2],
                        'cancel_before_hours' => ['label' => 'ยกเลิกนัดได้ก่อนเวลาอย่างน้อย (ชั่วโมง)', 'desc' => 'ผู้รับบริการต้องกดยกเลิกก่อนถึงเวลานัดหมาย', 'min' => 0, 'max' => 720, 'default' => 24],
                        'reschedule_before_hours' => ['label' => 'เลื่อนนัดได้ก่อนเวลาอย่างน้อย (ชั่วโมง)', 'desc' => 'กำหนดเวลาอนุญาตให้เลื่อนรอบเวลาล่วงหน้า', 'min' => 0, 'max' => 720, 'default' => 24],
                        'max_active_bookings_per_customer' => ['label' => 'จำนวนคิวที่ยังไม่เสร็จสูงสุดต่อคน', 'desc' => 'ป้องกันการจองคิวกักตุนพร้อมกันหลายรายการ', 'min' => 1, 'max' => 50, 'default' => 3],
                        'default_slot_capacity' => ['label' => 'ความจุผู้รับบริการเริ่มต้นต่อรอบ (คน)', 'desc' => 'จำนวนผู้รับบริการสูงสุดต่อหนึ่งช่วงเวลาหากไม่ได้ระบุเป็นพิเศษ', 'min' => 1, 'max' => 100, 'default' => 5],
                    ];
                @endphp

                @foreach($bookingFields as $key => $field)
                    <div class="rounded-xl border border-slate-100 bg-slate-50/50 p-4">
                        <label class="mb-1 block text-xs font-semibold text-slate-800">{{ $field['label'] }} <span class="text-rose-500">*</span></label>
                        <input type="number" min="{{ $field['min'] }}" max="{{ $field['max'] }}" name="{{ $key }}" required value="{{ old($key, $settings->get($key)?->value ?? $field['default']) }}" class="w-full rounded-xl border-slate-300 text-sm font-mono focus:border-brand-500 focus:ring-brand-200">
                        <p class="mt-1 text-[11px] text-slate-500">{{ $field['desc'] }}</p>
                    </div>
                @endforeach

                <div class="sm:col-span-2 flex justify-end pt-2">
                    <button type="submit" class="inline-flex items-center gap-2 rounded-xl bg-brand-700 px-5 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-brand-800 transition">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        บันทึกเงื่อนไขการจอง
                    </button>
                </div>
            </form>
        </section>

        {{-- ระบบแจ้งเตือน --}}
        <section class="rounded-2xl border border-slate-100 bg-white p-6 shadow-sm">
            <div class="border-b border-slate-100 pb-4 mb-5">
                <h2 class="text-lg font-bold text-slate-800 flex items-center gap-2">
                    <svg class="h-5 w-5 text-brand-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                    การแจ้งเตือน (Notifications)
                </h2>
                <p class="mt-0.5 text-xs text-slate-500">เลือกช่องทางการสื่อสารและเหตุการณ์ที่ต้องการส่งข้อความแจ้งเตือนอัตโนมัติ</p>
            </div>

            @php($channels = $settings->get('notification_channels')?->value ? json_decode($settings->get('notification_channels')->value, true) : ['email'])
            <form method="post" action="{{ route('admin.settings.notifications') }}" class="space-y-5">
                @csrf
                @method('PUT')

                <div>
                    <label class="mb-2 block text-xs font-bold uppercase tracking-wider text-slate-600">ช่องทางการแจ้งเตือนที่เปิดใช้</label>
                    <div class="grid gap-3 sm:grid-cols-3">
                        @foreach(['email' => ['label' => 'อีเมล (Email)', 'desc' => 'ส่งผ่าน Mail Driver ในระบบ'], 'sms' => ['label' => 'SMS มือถือ', 'desc' => 'เตรียมเชื่อมต่อ SMS Gateway'], 'line' => ['label' => 'LINE OA', 'desc' => 'แจ้งเตือนผ่านบัญชี LINE Official']] as $key => $ch)
                            <label class="flex items-start gap-3 rounded-xl border border-slate-200 bg-slate-50/50 p-3.5 hover:bg-slate-50 cursor-pointer transition">
                                <input type="checkbox" name="channels[]" value="{{ $key }}" @checked(in_array($key, $channels ?? [], true)) class="mt-0.5 h-4 w-4 rounded border-slate-300 text-brand-600 focus:ring-brand-500">
                                <div>
                                    <span class="block text-xs font-semibold text-slate-800">{{ $ch['label'] }}</span>
                                    <span class="block text-[11px] text-slate-400">{{ $ch['desc'] }}</span>
                                </div>
                            </label>
                        @endforeach
                    </div>
                </div>

                <div class="border-t border-slate-100 pt-4">
                    <label class="mb-2 block text-xs font-bold uppercase tracking-wider text-slate-600">เหตุการณ์ที่ส่งการแจ้งเตือน</label>
                    <div class="grid gap-3 sm:grid-cols-3">
                        @foreach(['booking_created' => 'แจ้งเมื่อจองคิวสำเร็จ', 'booking_cancelled' => 'แจ้งเมื่อมีการยกเลิกคิว', 'booking_reminder' => 'แจ้งเตือนล่วงหน้าก่อนวันนัด'] as $key => $label)
                            <label class="flex items-center gap-2.5 rounded-xl border border-slate-200 bg-slate-50/50 p-3 hover:bg-slate-50 cursor-pointer transition">
                                <input type="checkbox" name="notify_{{ $key }}" value="1" @checked((bool)($settings->get('notify_'.$key)?->value ?? '1')) class="h-4 w-4 rounded border-slate-300 text-brand-600 focus:ring-brand-500">
                                <span class="text-xs font-medium text-slate-800">{{ $label }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>

                <div class="max-w-xs">
                    <label class="mb-1 block text-xs font-semibold text-slate-700">ส่งเตือนก่อนเวลานัดหมาย (ชั่วโมง) <span class="text-rose-500">*</span></label>
                    <input type="number" name="reminder_before_hours" min="1" max="720" value="{{ old('reminder_before_hours', $settings->get('reminder_before_hours')?->value ?? 24) }}" required class="w-full rounded-xl border-slate-300 text-sm font-mono focus:border-brand-500 focus:ring-brand-200">
                    <p class="mt-1 text-[11px] text-slate-400">ระบบจะประมวลผลคำสั่งส่งแจ้งเตือนทุกชั่วโมงตามระยะเวลานี้</p>
                </div>

                <div class="flex justify-end pt-2 border-t border-slate-100">
                    <button type="submit" class="inline-flex items-center gap-2 rounded-xl bg-brand-700 px-5 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-brand-800 transition">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        บันทึกการตั้งค่าการแจ้งเตือน
                    </button>
                </div>
            </form>
        </section>

        {{-- ข้อความเว็บไซต์และ PDPA --}}
        <section class="rounded-2xl border border-slate-100 bg-white p-6 shadow-sm">
            <div class="border-b border-slate-100 pb-4 mb-5">
                <h2 class="text-lg font-bold text-slate-800 flex items-center gap-2">
                    <svg class="h-5 w-5 text-brand-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    ข้อความเว็บไซต์ ประกาศ และข้อตกลง PDPA
                </h2>
                <p class="mt-0.5 text-xs text-slate-500">ปรับแต่งข้อความทางกฎหมาย นโยบายการยกเลิก และประกาศสำคัญหน้าเว็บไซต์</p>
            </div>

            <form method="post" action="{{ route('admin.settings.website') }}" class="space-y-4">
                @csrf
                @method('PUT')

                <div>
                    <label class="mb-1 block text-xs font-semibold text-slate-700">ข้อความขอความยินยอมคุ้มครองข้อมูลส่วนบุคคล (PDPA Consent) <span class="text-rose-500">*</span></label>
                    <textarea name="pdpa_consent_text" rows="3" required class="w-full rounded-xl border-slate-300 text-sm focus:border-brand-500 focus:ring-brand-200">{{ old('pdpa_consent_text', $settings->get('pdpa_consent_text')?->value) }}</textarea>
                    <p class="mt-1 text-[11px] text-slate-400">แสดงในขั้นตอนการกรอกข้อมูลและยืนยันการจองคิวของผู้รับบริการ</p>
                </div>

                <div>
                    <label class="mb-1 block text-xs font-semibold text-slate-700">นโยบายและเงื่อนไขการยกเลิก/เลื่อนนัด</label>
                    <textarea name="cancellation_policy" rows="2" class="w-full rounded-xl border-slate-300 text-sm focus:border-brand-500 focus:ring-brand-200">{{ old('cancellation_policy', $settings->get('cancellation_policy')?->value) }}</textarea>
                </div>

                <div>
                    <label class="mb-1 block text-xs font-semibold text-slate-700">ประกาศสั้นบนหน้าแรกของเว็บไซต์</label>
                    <textarea name="clinic_notice" rows="2" placeholder="เช่น ขอความกรุณามาถึงก่อนเวลานัดหมาย 15 นาที เพื่อตรวจคัดกรองเบื้องต้น" class="w-full rounded-xl border-slate-300 text-sm focus:border-brand-500 focus:ring-brand-200">{{ old('clinic_notice', $settings->get('clinic_notice')?->value) }}</textarea>
                </div>

                <div>
                    <label class="mb-1 block text-xs font-semibold text-slate-700">ข้อความระบุเวลาทำการเพิ่มเติม</label>
                    <input name="opening_hours_text" value="{{ old('opening_hours_text', $settings->get('opening_hours_text')?->value) }}" placeholder="เช่น จันทร์ - ศุกร์ 08:30 - 16:30 น. (เว้นวันหยุดนักขัตฤกษ์)" class="w-full rounded-xl border-slate-300 text-sm focus:border-brand-500 focus:ring-brand-200">
                </div>

                <div class="flex justify-end pt-2 border-t border-slate-100">
                    <button type="submit" class="inline-flex items-center gap-2 rounded-xl bg-brand-700 px-5 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-brand-800 transition">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        บันทึกเนื้อหาและข้อความเว็บไซต์
                    </button>
                </div>
            </form>
        </section>
    </div>
</div>
@endsection

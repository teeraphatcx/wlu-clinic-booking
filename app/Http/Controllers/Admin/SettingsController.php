<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Setting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class SettingsController extends Controller
{
    public function edit(): View
    {
        return view('admin.settings.edit', [
            'siteLogo' => Setting::assetPath('site_logo'),
            'siteHeader' => Setting::assetPath('site_header_image'),
            'siteHeaderLink' => Setting::get('site_header_link'),
            'settings' => Setting::query()->get()->keyBy('key'),
        ]);
    }

    public function updateGeneral(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'clinic_name_th' => ['required', 'string', 'max:255'], 'clinic_name_en' => ['nullable', 'string', 'max:255'],
            'hospital_name' => ['required', 'string', 'max:255'], 'clinic_phone' => ['required', 'string', 'max:30'],
            'clinic_email' => ['nullable', 'email', 'max:255'], 'clinic_address' => ['required', 'string', 'max:2000'],
            'clinic_description' => ['nullable', 'string', 'max:5000'], 'clinic_map_url' => ['nullable', 'url', 'max:2048'],
        ]);
        foreach ($data as $key => $value) {
            Setting::set($key, $value, 'string', 'general', 'ข้อมูลทั่วไปของคลินิก', true);
        }
        AuditLog::record(auth('admin')->id(), 'general_settings_updated', null, null, $data);

        return back()->with('success', 'บันทึกข้อมูลทั่วไปแล้ว');
    }

    public function updateBooking(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'booking_advance_days' => ['required', 'integer', 'between:1,365'], 'booking_close_before_hours' => ['required', 'integer', 'between:0,720'],
            'cancel_before_hours' => ['required', 'integer', 'between:0,720'], 'reschedule_before_hours' => ['required', 'integer', 'between:0,720'],
            'max_active_bookings_per_customer' => ['required', 'integer', 'between:1,50'], 'default_slot_capacity' => ['required', 'integer', 'between:1,100'],
        ]);
        foreach ($data as $key => $value) {
            Setting::set($key, $value, 'integer', 'booking', 'เงื่อนไขการจองคิว', $key !== 'default_slot_capacity');
        }
        AuditLog::record(auth('admin')->id(), 'booking_settings_updated', null, null, $data);

        return back()->with('success', 'บันทึกเงื่อนไขการจองแล้ว');
    }

    public function updateNotifications(Request $request): RedirectResponse
    {
        $data = $request->validate(['channels' => ['nullable', 'array'], 'channels.*' => ['in:email,sms,line'], 'reminder_before_hours' => ['required', 'integer', 'between:1,720']]);
        Setting::set('notification_channels', $data['channels'] ?? [], 'json', 'notification', 'ช่องทางการแจ้งเตือน (รองรับ Email, SMS, LINE OA)', false);
        Setting::set('reminder_before_hours', $data['reminder_before_hours'], 'integer', 'notification', 'แจ้งเตือนก่อนนัดกี่ชั่วโมง', false);
        foreach (['booking_created', 'booking_cancelled', 'booking_reminder'] as $event) {
            Setting::set('notify_'.$event, $request->boolean('notify_'.$event), 'boolean', 'notification', 'เปิด/ปิดการแจ้งเตือน', false);
        }
        AuditLog::record(auth('admin')->id(), 'notification_settings_updated', null, null, $data);

        return back()->with('success', 'บันทึกการตั้งค่าการแจ้งเตือนแล้ว');
    }

    public function updateWebsite(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'pdpa_consent_text' => ['required', 'string', 'max:5000'], 'cancellation_policy' => ['nullable', 'string', 'max:5000'],
            'clinic_notice' => ['nullable', 'string', 'max:1000'], 'opening_hours_text' => ['nullable', 'string', 'max:1000'],
        ]);
        foreach ($data as $key => $value) {
            Setting::set($key, $value, 'string', 'website', 'ข้อความที่แสดงบนเว็บไซต์', true);
        }
        AuditLog::record(auth('admin')->id(), 'website_content_updated', null, null, $data);

        return back()->with('success', 'บันทึกเนื้อหาเว็บไซต์แล้ว');
    }

    public function updateLogo(Request $request): RedirectResponse
    {
        $request->validate([
            'logo' => ['required', 'image', 'mimes:png,jpg,jpeg,webp,svg', 'max:2048'],
        ], [
            'logo.required' => 'กรุณาเลือกไฟล์โลโก้',
            'logo.image' => 'ไฟล์ต้องเป็นรูปภาพเท่านั้น',
            'logo.mimes' => 'รองรับไฟล์ PNG, JPG, WEBP หรือ SVG เท่านั้น',
            'logo.max' => 'ขนาดไฟล์ต้องไม่เกิน 2 MB',
        ]);

        $oldLogo = Setting::get('site_logo');
        $logoPath = $request->file('logo')->store('settings', 'public');

        Setting::set(
            'site_logo',
            $logoPath,
            'string',
            'website',
            'โลโก้ที่แสดงบนเว็บไซต์และหลังบ้าน',
            true
        );

        if ($oldLogo && Storage::disk('public')->exists($oldLogo)) {
            Storage::disk('public')->delete($oldLogo);
        }

        return back()->with('success', 'อัปโหลดโลโก้และบันทึกการตั้งค่าแล้ว');
    }

    public function updateHeader(Request $request): RedirectResponse
    {
        $request->validate([
            'header_image' => ['nullable', 'image', 'mimes:png,jpg,jpeg,webp', 'max:4096'],
        ], [
            'header_image.image' => 'ไฟล์ต้องเป็นรูปภาพเท่านั้น',
            'header_image.mimes' => 'รองรับไฟล์ PNG, JPG หรือ WEBP เท่านั้น',
            'header_image.max' => 'ขนาดไฟล์ต้องไม่เกิน 4 MB',
        ]);

        if ($request->hasFile('header_image')) {
            $oldHeader = Setting::get('site_header_image');
            $headerPath = $request->file('header_image')->store('settings', 'public');
            Setting::set('site_header_image', $headerPath, 'string', 'website', 'ภาพ Header แบบยาวของเว็บไซต์', true);

            if ($oldHeader && Storage::disk('public')->exists($oldHeader)) {
                Storage::disk('public')->delete($oldHeader);
            }
        } elseif (! Setting::get('site_header_image')) {
            return back()->withErrors(['header_image' => 'กรุณาเลือกภาพ Header แบบยาวก่อนบันทึก'])->withInput();
        }

        return back()->with('success', 'อัปโหลดภาพ Header แบบยาวแล้ว');
    }

    public function updateHeaderLink(Request $request): RedirectResponse
    {
        $request->validate([
            'header_link' => ['nullable', 'string', 'max:2048', 'regex:/^(https?:\/\/|\/)/i'],
        ], [
            'header_link.regex' => 'ลิงก์ต้องขึ้นต้นด้วย http://, https:// หรือ /',
            'header_link.max' => 'ลิงก์ต้องมีความยาวไม่เกิน 2,048 ตัวอักษร',
        ]);

        Setting::set('site_header_link', trim((string) $request->input('header_link')), 'string', 'website', 'ลิงก์เมื่อกดภาพ Header แบบยาว', true);

        return back()->with('success', 'บันทึกลิงก์ของ Header แล้ว');
    }

    public function removeLogo(): RedirectResponse
    {
        $logoPath = Setting::get('site_logo');

        if ($logoPath && Storage::disk('public')->exists($logoPath)) {
            Storage::disk('public')->delete($logoPath);
        }

        Setting::set('site_logo', '', 'string', 'website', 'โลโก้ที่แสดงบนเว็บไซต์และหลังบ้าน', true);

        return back()->with('success', 'ลบโลโก้แล้ว ระบบจะกลับไปใช้ไอคอนเริ่มต้น');
    }

    public function removeHeader(): RedirectResponse
    {
        $headerPath = Setting::get('site_header_image');

        if ($headerPath && Storage::disk('public')->exists($headerPath)) {
            Storage::disk('public')->delete($headerPath);
        }

        Setting::set('site_header_image', '', 'string', 'website', 'ภาพ Header แบบยาวของเว็บไซต์', true);
        Setting::set('site_header_link', '', 'string', 'website', 'ลิงก์เมื่อกดภาพ Header แบบยาว', true);

        return back()->with('success', 'ลบภาพ Header แล้ว');
    }
}

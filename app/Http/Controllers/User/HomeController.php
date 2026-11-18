<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\ClinicHoliday;
use App\Models\ClinicSchedule;
use App\Models\Content;
use App\Models\Service;
use App\Models\Setting;
use Carbon\Carbon;
use Illuminate\View\View;

class HomeController extends Controller
{
    /**
     * Display the clinic homepage.
     */
    public function index(): View
    {
        $today = Carbon::today();
        $todayStr = $today->toDateString();

        $clinicSchedules = ClinicSchedule::allCached();
        $todaySchedule = $clinicSchedules->firstWhere('day_of_week', $today->dayOfWeek);

        $holidayToday = ClinicHoliday::whereBetween('date', [$today->copy()->startOfDay(), $today->copy()->endOfDay()])->first();
        $isOpenToday = (bool) $todaySchedule?->is_open && ! $holidayToday?->is_full_day;

        $services = Service::active()
            ->ordered()
            ->take(6)
            ->get();

        $news = Content::published()
            ->whereIn('type', ['news', 'announcement', 'promotion'])
            ->take(3)
            ->get();

        $clinicSettings = [
            'name_th' => Setting::get('clinic_name_th', 'คลินิกแพทย์แผนไทย'),
            'name_en' => Setting::get('clinic_name_en', 'Thai Traditional Medicine Clinic'),
            'hospital' => Setting::get('hospital_name', 'โรงพยาบาลศูนย์การแพทย์ มหาวิทยาลัยวลัยลักษณ์'),
            'phone' => Setting::get('clinic_phone', '0-7567-3000'),
            'address' => Setting::get('clinic_address', '222 ต.ไทยบุรี อ.ท่าศาลา จ.นครศรีธรรมราช 80160'),
            'description' => Setting::get('clinic_description', 'บริการตรวจรักษาและฟื้นฟูสุขภาพด้วยศาสตร์แพทย์แผนไทย'),
            'notice' => Setting::get('clinic_notice', ''),
            'map_url' => Setting::get('clinic_map_url', ''),
            'opening_hours_text' => Setting::get('opening_hours_text', ''),
        ];

        return view('user.home.index', compact(
            'services',
            'news',
            'isOpenToday',
            'todaySchedule',
            'clinicSchedules',
            'holidayToday',
            'clinicSettings'
        ));
    }
}

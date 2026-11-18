<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\ClinicSchedule;
use App\Models\Setting;
use Illuminate\View\View;

class PageController extends Controller
{
    /**
     * Display the Privacy Policy (PDPA) page.
     */
    public function privacy(): View
    {
        $pdpaConsentText = Setting::get('pdpa_consent_text');
        $clinicName = Setting::get('clinic_name_th', 'คลินิกแพทย์แผนไทย');
        $hospitalName = Setting::get('hospital_name', 'โรงพยาบาลศูนย์การแพทย์ มหาวิทยาลัยวลัยลักษณ์');

        return view('user.pages.privacy', compact('pdpaConsentText', 'clinicName', 'hospitalName'));
    }

    /**
     * Display the Contact & Location page.
     */
    public function contact(): View
    {
        $clinicSettings = [
            'name_th' => Setting::get('clinic_name_th', 'คลินิกแพทย์แผนไทย'),
            'name_en' => Setting::get('clinic_name_en', 'Thai Traditional Medicine Clinic'),
            'hospital' => Setting::get('hospital_name', 'โรงพยาบาลศูนย์การแพทย์ มหาวิทยาลัยวลัยลักษณ์'),
            'phone' => Setting::get('clinic_phone', '0-7567-3000'),
            'address' => Setting::get('clinic_address', '222 ต.ไทยบุรี อ.ท่าศาลา จ.นครศรีธรรมราช 80160'),
            'email' => Setting::get('clinic_email', ''),
            'map_url' => Setting::get('clinic_map_url', ''),
            'schedules' => ClinicSchedule::orderBy('day_of_week')->get(),
        ];

        return view('user.pages.contact', compact('clinicSettings'));
    }
}

<?php

use App\Http\Controllers\Admin\AuthController as AdminAuthController;
use App\Http\Controllers\Admin\BookingController as AdminBookingController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\ManagementController as AdminManagementController;
use App\Http\Controllers\Admin\ReportController as AdminReportController;
use App\Http\Controllers\Admin\SettingsController as AdminSettingsController;
use App\Http\Controllers\User\BookingController;
use App\Http\Controllers\User\HomeController;
use App\Http\Controllers\User\PageController;
use App\Http\Controllers\User\ServiceController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public Web Routes - Thai Traditional Medicine Clinic
|--------------------------------------------------------------------------
*/

// Home & Informational
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/privacy-policy', [PageController::class, 'privacy'])->name('privacy');
Route::get('/contact', [PageController::class, 'contact'])->name('contact');

// Services
Route::get('/services', [ServiceController::class, 'index'])->name('services.index');
Route::get('/services/{service}', [ServiceController::class, 'show'])->name('services.show');

// Booking Flow
Route::get('/booking', [BookingController::class, 'index'])->name('booking.index');
Route::get('/booking/{service}', [BookingController::class, 'selectDate'])->name('booking.select-date');
Route::get('/booking/{service}/slots', [BookingController::class, 'getSlots'])->name('booking.slots');
Route::get('/booking/{service}/therapists', [BookingController::class, 'getTherapists'])->name('booking.therapists');
Route::post('/booking', [BookingController::class, 'store'])
    ->middleware('throttle:booking')
    ->name('booking.store');
Route::get('/booking/confirm/{token}', [BookingController::class, 'confirm'])->name('booking.confirm');

// Check & Manage Booking
Route::get('/check-booking', [BookingController::class, 'checkForm'])->name('booking.check-form');
Route::post('/check-booking', [BookingController::class, 'check'])
    ->middleware('throttle:check-booking')
    ->name('booking.check');
Route::get('/check-booking/{bookingNo}', [BookingController::class, 'show'])->name('booking.show');
Route::post('/check-booking/{bookingNo}/cancel', [BookingController::class, 'cancel'])->name('booking.cancel');
Route::get('/check-booking/{bookingNo}/reschedule', [BookingController::class, 'rescheduleForm'])->name('booking.reschedule-form');
Route::post('/check-booking/{bookingNo}/reschedule', [BookingController::class, 'reschedule'])->name('booking.reschedule');

// เจ้าหน้าที่ (ใช้ guard แยกจากผู้รับบริการ)
Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('/login', [AdminAuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AdminAuthController::class, 'login'])->middleware('throttle:admin-login')->name('login.submit');
    Route::middleware('admin.auth')->group(function () {
        Route::post('/logout', [AdminAuthController::class, 'logout'])->name('logout');
        Route::get('/', fn () => redirect()->route('admin.dashboard'))->name('home');
        Route::get('/dashboard', [AdminDashboardController::class, 'index'])->middleware('admin.permission:reports.view')->name('dashboard');
        Route::get('/bookings', [AdminBookingController::class, 'index'])->middleware('admin.permission:bookings.view')->name('bookings.index');
        Route::post('/bookings', [AdminBookingController::class, 'store'])->middleware('admin.permission:bookings.create')->name('bookings.store');
        Route::patch('/bookings/{booking}/reschedule', [AdminBookingController::class, 'reschedule'])->middleware('admin.permission:bookings.edit')->name('bookings.reschedule');
        Route::get('/bookings/{booking}/history', [AdminBookingController::class, 'history'])->middleware('admin.permission:bookings.view')->name('bookings.history');
        Route::patch('/bookings/{booking}/status', [AdminBookingController::class, 'status'])->middleware('admin.permission:bookings.status')->name('bookings.status');
        Route::get('/users', [AdminManagementController::class, 'users'])->middleware('admin.permission:customers.view|admins.view')->name('users.index');
        Route::get('/customers', [AdminManagementController::class, 'customers'])->middleware('admin.permission:customers.view')->name('customers.index');
        Route::get('/customers/{customer}', [AdminManagementController::class, 'customer'])->middleware('admin.permission:customers.view')->name('customers.show');
        Route::post('/customers', [AdminManagementController::class, 'saveCustomer'])->middleware('admin.permission:customers.create')->name('customers.store');
        Route::put('/customers/{customer}', [AdminManagementController::class, 'saveCustomer'])->middleware('admin.permission:customers.edit')->name('customers.update');
        Route::patch('/customers/{customer}/status', [AdminManagementController::class, 'toggleCustomer'])->middleware('admin.permission:customers.edit')->name('customers.status');
        Route::get('/services', [AdminManagementController::class, 'services'])->middleware('admin.permission:services.view')->name('services.index');
        Route::post('/services', [AdminManagementController::class, 'saveService'])->middleware('admin.permission:services.create')->name('services.store');
        Route::put('/services/{service}', [AdminManagementController::class, 'saveService'])->middleware('admin.permission:services.edit')->name('services.update');
        Route::patch('/services/{service}/status', [AdminManagementController::class, 'toggleService'])->middleware('admin.permission:services.edit')->name('services.status');
        Route::get('/therapists', [AdminManagementController::class, 'therapists'])->middleware('admin.permission:therapists.view')->name('therapists.index');
        Route::post('/therapists', [AdminManagementController::class, 'saveTherapist'])->middleware('admin.permission:therapists.create')->name('therapists.store');
        Route::put('/therapists/{therapist}', [AdminManagementController::class, 'saveTherapist'])->middleware('admin.permission:therapists.edit')->name('therapists.update');
        Route::post('/therapists/{therapist}/schedules', [AdminManagementController::class, 'saveTherapistSchedule'])->middleware('admin.permission:therapists.schedule')->name('therapists.schedules.store');
        Route::post('/therapists/{therapist}/days-off', [AdminManagementController::class, 'addTherapistDayOff'])->middleware('admin.permission:therapists.schedule')->name('therapists.days-off.store');
        Route::post('/therapists/{therapist}/blocked-periods', [AdminManagementController::class, 'addTherapistBlock'])->middleware('admin.permission:therapists.schedule')->name('therapists.blocks.store');
        Route::get('/schedules', [AdminManagementController::class, 'schedules'])->middleware('admin.permission:schedules.view')->name('schedules.index');
        Route::put('/schedules/clinic', [AdminManagementController::class, 'saveClinicSchedules'])->middleware('admin.permission:schedules.edit')->name('schedules.clinic.update');
        Route::put('/schedules/time-slots', [AdminManagementController::class, 'saveSlots'])->middleware('admin.permission:schedules.edit')->name('schedules.slots.update');
        Route::post('/schedules/holidays', [AdminManagementController::class, 'saveHoliday'])->middleware('admin.permission:schedules.edit')->name('schedules.holidays.store');
        Route::put('/schedules/holidays/{holiday}', [AdminManagementController::class, 'saveHoliday'])->middleware('admin.permission:schedules.edit')->name('schedules.holidays.update');
        Route::delete('/schedules/holidays/{holiday}', [AdminManagementController::class, 'deleteHoliday'])->middleware('admin.permission:schedules.edit')->name('schedules.holidays.delete');
        Route::get('/contents', [AdminManagementController::class, 'contents'])->middleware('admin.permission:contents.view')->name('contents.index');
        Route::post('/contents', [AdminManagementController::class, 'saveContent'])->middleware('admin.permission:contents.create')->name('contents.store');
        Route::put('/contents/{content}', [AdminManagementController::class, 'saveContent'])->middleware('admin.permission:contents.edit')->name('contents.update');
        Route::patch('/contents/{content}/status', [AdminManagementController::class, 'toggleContent'])->middleware('admin.permission:contents.edit')->name('contents.status');
        Route::get('/admins', [AdminManagementController::class, 'admins'])->middleware('admin.permission:admins.view')->name('admins.index');
        Route::post('/admins', [AdminManagementController::class, 'saveAdmin'])->middleware('admin.permission:admins.create')->name('admins.store');
        Route::put('/admins/{admin}', [AdminManagementController::class, 'saveAdmin'])->middleware('admin.permission:admins.edit')->name('admins.update');
        Route::patch('/admins/{admin}/status', [AdminManagementController::class, 'toggleAdmin'])->middleware('admin.permission:admins.edit')->name('admins.status');
        Route::put('/roles/{role}/permissions', [AdminManagementController::class, 'saveRolePermissions'])->middleware('admin.permission:admins.edit')->name('roles.permissions');
        Route::get('/settings', [AdminSettingsController::class, 'edit'])->middleware('admin.permission:settings.view')->name('settings.edit');
        Route::put('/settings/general', [AdminSettingsController::class, 'updateGeneral'])->middleware('admin.permission:settings.edit')->name('settings.general');
        Route::put('/settings/booking', [AdminSettingsController::class, 'updateBooking'])->middleware('admin.permission:settings.edit')->name('settings.booking');
        Route::put('/settings/notifications', [AdminSettingsController::class, 'updateNotifications'])->middleware('admin.permission:settings.edit')->name('settings.notifications');
        Route::put('/settings/website', [AdminSettingsController::class, 'updateWebsite'])->middleware('admin.permission:settings.edit')->name('settings.website');
        Route::post('/settings/logo', [AdminSettingsController::class, 'updateLogo'])->middleware('admin.permission:settings.edit')->name('settings.logo');
        Route::delete('/settings/logo', [AdminSettingsController::class, 'removeLogo'])->middleware('admin.permission:settings.edit')->name('settings.logo.remove');
        Route::post('/settings/header', [AdminSettingsController::class, 'updateHeader'])->middleware('admin.permission:settings.edit')->name('settings.header');
        Route::post('/settings/header-link', [AdminSettingsController::class, 'updateHeaderLink'])->middleware('admin.permission:settings.edit')->name('settings.header.link');
        Route::delete('/settings/header', [AdminSettingsController::class, 'removeHeader'])->middleware('admin.permission:settings.edit')->name('settings.header.remove');
        Route::get('/reports/bookings.xlsx', [AdminReportController::class, 'excel'])->middleware('admin.permission:reports.export')->name('reports.bookings.excel');
        Route::get('/reports/bookings.pdf', [AdminReportController::class, 'pdf'])->middleware('admin.permission:reports.export')->name('reports.bookings.pdf');
        Route::get('/reports', [AdminReportController::class, 'index'])->middleware('admin.permission:reports.view')->name('reports.index');
    });
});

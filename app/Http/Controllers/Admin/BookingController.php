<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Booking;
use App\Models\BookingHistory;
use App\Models\Customer;
use App\Models\Service as ClinicService;
use App\Models\Therapist;
use App\Models\TimeSlot;
use App\Services\BookingService;
use Carbon\Carbon;
use Illuminate\Http\Request;

class BookingController extends Controller
{
    public function index(Request $request)
    {
        $bookings = Booking::query()
            ->select(['id', 'booking_no', 'created_at', 'booking_date', 'time_slot_id', 'customer_id', 'service_id', 'therapist_id', 'status'])
            ->with([
                'customer:id,first_name,last_name,phone',
                'service:id,name_th,price,duration_minutes',
                'therapist:id,first_name,last_name,nickname',
                'timeSlot:id,start_time,end_time',
            ])
            ->when($request->status, fn ($q, $status) => $q->where('status', $status))
            ->when($request->date, fn ($q, $date) => $q->whereBetween('booking_date', [Carbon::parse($date)->startOfDay(), Carbon::parse($date)->endOfDay()]))
            ->when($request->search, fn ($q, $search) => $q->where(function ($query) use ($search) {
                $query->where('booking_no', 'like', "%{$search}%")
                    ->orWhereHas('customer', fn ($customer) => $customer->where('phone', 'like', "%{$search}%")->orWhere('first_name', 'like', "%{$search}%"));
            }))
            ->latest('booking_date')->latest('id')->paginate(20)->withQueryString();

        return view('admin.bookings.index', [
            'bookings' => $bookings,
            'services' => ClinicService::where('is_active', true)->orderBy('sort_order')->get(),
            'customers' => Customer::where('is_active', true)->orderBy('last_name')->limit(500)->get(),
            'therapists' => Therapist::where('is_active', true)->orderBy('first_name')->get(),
            'slots' => TimeSlot::active()->get(),
        ]);
    }

    public function store(Request $request, BookingService $service)
    {
        $data = $request->validate([
            'service_id' => ['required', 'exists:services,id'], 'booking_date' => ['required', 'date', 'after_or_equal:today'],
            'time_slot_id' => ['required', 'exists:time_slots,id'], 'therapist_id' => ['nullable', 'exists:therapists,id'],
            'first_name' => ['required', 'string', 'max:100'], 'last_name' => ['required', 'string', 'max:100'],
            'phone' => ['required', 'regex:/^0[0-9]{8,9}$/'], 'email' => ['nullable', 'email', 'max:255'],
            'notes' => ['nullable', 'string', 'max:500'],
        ]);
        try {
            $booking = $service->createBooking([...$data, 'pdpa_accepted' => true, 'ip_address' => $request->ip()]);
            BookingHistory::where('booking_id', $booking->id)->latest('id')->first()?->update(['admin_id' => auth('admin')->id(), 'notes' => 'เจ้าหน้าที่สร้างรายการจอง']);
            AuditLog::record(auth('admin')->id(), 'booking_created_by_staff', $booking, null, ['booking_no' => $booking->booking_no]);

            return back()->with('success', "สร้างรายการจอง {$booking->booking_no} เรียบร้อยแล้ว");
        } catch (\InvalidArgumentException $e) {
            return back()->withInput()->withErrors(['booking' => $e->getMessage()]);
        } catch (\Throwable $e) {
            return back()->withInput()->withErrors(['booking' => 'เกิดข้อผิดพลาดในการสร้างการจอง: '.$e->getMessage()]);
        }
    }

    public function reschedule(Request $request, Booking $booking, BookingService $service)
    {
        $data = $request->validate([
            'booking_date' => ['required', 'date', 'after_or_equal:today'],
            'time_slot_id' => ['required', 'exists:time_slots,id'],
            'therapist_id' => ['nullable', 'exists:therapists,id'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);
        try {
            $oldValues = ['booking_date' => $booking->booking_date->toDateString(), 'time_slot_id' => $booking->time_slot_id, 'therapist_id' => $booking->therapist_id];
            $service->rescheduleBooking($booking, $data['booking_date'], (int) $data['time_slot_id'], $data['therapist_id'] ? (int) $data['therapist_id'] : null, auth('admin')->user());
            AuditLog::record(auth('admin')->id(), 'booking_rescheduled', $booking, $oldValues, ['booking_date' => $booking->fresh()->booking_date->toDateString(), 'time_slot_id' => $booking->fresh()->time_slot_id, 'therapist_id' => $booking->fresh()->therapist_id]);

            return back()->with('success', 'เปลี่ยนวันและเวลานัดหมายแล้ว');
        } catch (\InvalidArgumentException $e) {
            return back()->withInput()->withErrors(['reschedule' => $e->getMessage()]);
        } catch (\Throwable $e) {
            return back()->withInput()->withErrors(['reschedule' => 'เกิดข้อผิดพลาดในการเปลี่ยนวันเวลานัดหมาย: '.$e->getMessage()]);
        }
    }

    public function history(Booking $booking)
    {
        $booking->load(['customer', 'service', 'therapist', 'timeSlot', 'histories.admin']);

        return view('admin.bookings.history', compact('booking'));
    }

    public function status(Request $request, Booking $booking, BookingService $service)
    {
        $data = $request->validate(['status' => ['required', 'in:pending,confirmed,completed,cancelled,no_show'], 'notes' => ['nullable', 'string', 'max:1000']]);
        $oldStatus = $booking->status;
        $service->changeStatus($booking, $data['status'], auth('admin')->user(), $data['notes'] ?? null);
        AuditLog::record(auth('admin')->id(), 'status_change', $booking, ['status' => $oldStatus], ['status' => $data['status']]);

        return back()->with('success', 'เปลี่ยนสถานะการจองเรียบร้อยแล้ว');
    }
}

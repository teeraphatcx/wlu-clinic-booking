<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\BookingStoreRequest;
use App\Models\Booking;
use App\Models\Service;
use App\Models\Setting;
use App\Services\AvailabilityService;
use App\Services\BookingService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use InvalidArgumentException;

class BookingController extends Controller
{
    public function __construct(
        protected AvailabilityService $availabilityService,
        protected BookingService $bookingService
    ) {}

    /**
     * Step 1: Select a service for booking.
     */
    public function index(): View
    {
        $services = Service::active()
            ->ordered()
            ->get();

        return view('user.booking.index', compact('services'));
    }

    /**
     * Step 2: Select Date, Time Slot, and Therapist for the service.
     */
    public function selectDate(Service $service, Request $request): View
    {
        if (! $service->is_active) {
            abort(404, 'บริการนี้ไม่เปิดให้จอง');
        }

        $month = (int) $request->get('month', Carbon::now()->month);
        $year = (int) $request->get('year', Carbon::now()->year);

        $calendarDates = $this->availabilityService->getAvailableDates($service, $month, $year);
        $pdpaText = Setting::get('pdpa_consent_text');

        return view('user.booking.select-date', compact('service', 'calendarDates', 'month', 'year', 'pdpaText'));
    }

    /**
     * AJAX: Get available time slots for a given date.
     */
    public function getSlots(Service $service, Request $request): JsonResponse
    {
        $date = $request->query('date');
        $therapistId = $request->query('therapist_id') ? (int) $request->query('therapist_id') : null;

        if (! $date) {
            return response()->json(['error' => 'จำเป็นต้องระบุวันที่'], 422);
        }

        $slots = $this->availabilityService->getAvailableSlots($service, $date, $therapistId);

        return response()->json([
            'date' => $date,
            'slots' => $slots,
        ]);
    }

    /**
     * AJAX: Get available therapists for a given date and time slot.
     */
    public function getTherapists(Service $service, Request $request): JsonResponse
    {
        $date = $request->query('date');
        $timeSlotId = (int) $request->query('time_slot_id');

        if (! $date || ! $timeSlotId) {
            return response()->json(['error' => 'จำเป็นต้องระบุวันที่และรอบเวลา'], 422);
        }

        $therapists = $this->availabilityService->getAvailableTherapists($service, $date, $timeSlotId);

        $formatted = collect($therapists)->map(function ($t) {
            return [
                'id' => $t->id,
                'name' => $t->fullName,
                'nickname' => $t->nickname,
                'displayName' => $t->displayName,
                'gender' => $t->gender,
                'bio' => $t->bio,
                'photo' => $t->photo,
                'photo_url' => $t->photo ? asset('storage/'.$t->photo) : null,
            ];
        });

        return response()->json([
            'date' => $date,
            'time_slot_id' => $timeSlotId,
            'therapists' => $formatted,
        ]);
    }

    /**
     * Store a newly created booking in storage.
     */
    public function store(BookingStoreRequest $request): RedirectResponse
    {
        try {
            $data = $request->validated();
            $data['ip_address'] = $request->ip();

            $booking = $this->bookingService->createBooking($data);
            session(['verified_phone_'.$booking->booking_no => $booking->customer->phone]);

            return redirect()
                ->route('booking.confirm', ['token' => $booking->booking_no])
                ->with('success', 'การจองคิวสำเร็จ กรุณาตรวจสอบข้อมูลและบันทึกรหัสการจอง');
        } catch (InvalidArgumentException $e) {
            return back()
                ->withInput()
                ->withErrors(['booking' => $e->getMessage()]);
        } catch (\Throwable $e) {
            return back()
                ->withInput()
                ->withErrors(['booking' => 'เกิดข้อผิดพลาดในการทำรายการ กรุณาลองใหม่อีกครั้ง ('.$e->getMessage().')']);
        }
    }

    /**
     * Confirmation screen after successful booking.
     */
    public function confirm(string $token): View
    {
        $booking = Booking::with(['service', 'therapist', 'timeSlot', 'customer'])
            ->where('booking_no', $token)
            ->firstOrFail();
        abort_unless(session('verified_phone_'.$token) === $booking->customer?->phone, 404);

        return view('user.booking.confirm', compact('booking'));
    }

    /**
     * Show booking check form.
     */
    public function checkForm(): View
    {
        return view('user.booking.check');
    }

    /**
     * Process booking lookup.
     */
    public function check(Request $request): RedirectResponse
    {
        $request->validate([
            'booking_no' => 'required|string',
            'phone' => 'required|string',
        ], [
            'booking_no.required' => 'กรุณากรอกรหัสการจอง',
            'phone.required' => 'กรุณากรอกเบอร์โทรศัพท์ที่ใช้จอง',
        ]);

        $bookingNo = trim($request->booking_no);
        $phone = trim($request->phone);

        $booking = Booking::where('booking_no', $bookingNo)
            ->whereHas('customer', function ($query) use ($phone) {
                $query->where('phone', $phone);
            })
            ->first();

        if (! $booking) {
            return back()
                ->withInput()
                ->withErrors(['check' => 'ไม่พบข้อมูลการจอง หรือข้อมูลเบอร์โทรศัพท์ไม่ตรงกับในระบบ']);
        }

        // Store phone in session to authorize viewing
        session(['verified_phone_'.$bookingNo => $phone]);

        return redirect()->route('booking.show', ['bookingNo' => $bookingNo]);
    }

    /**
     * Show booking details & history.
     */
    public function show(string $bookingNo): View
    {
        $booking = $this->authorizedBooking($bookingNo, ['service', 'therapist', 'timeSlot', 'customer', 'histories' => fn ($q) => $q->latest()]);

        $canCancel = $this->bookingService->canCancel($booking);
        $canReschedule = $this->bookingService->canReschedule($booking);

        return view('user.booking.show', compact('booking', 'canCancel', 'canReschedule'));
    }

    /**
     * Cancel an existing booking.
     */
    public function cancel(string $bookingNo, Request $request): RedirectResponse
    {
        $request->validate([
            'reason' => 'required|string|max:255',
        ], [
            'reason.required' => 'กรุณาระบุเหตุผลในการยกเลิก',
        ]);

        $booking = $this->authorizedBooking($bookingNo);

        try {
            $this->bookingService->cancelBooking($booking, $request->reason);

            return redirect()
                ->route('booking.show', ['bookingNo' => $bookingNo])
                ->with('success', 'ยกเลิกการจองคิวเรียบร้อยแล้ว');
        } catch (InvalidArgumentException $e) {
            return back()->withErrors(['cancel' => $e->getMessage()]);
        }
    }

    /**
     * Form to reschedule a booking.
     */
    public function rescheduleForm(string $bookingNo, Request $request): View|RedirectResponse
    {
        $booking = $this->authorizedBooking($bookingNo, ['service', 'therapist', 'timeSlot', 'customer']);

        $canReschedule = $this->bookingService->canReschedule($booking);
        if ($canReschedule !== true) {
            return redirect()
                ->route('booking.show', ['bookingNo' => $bookingNo])
                ->withErrors(['reschedule' => $canReschedule]);
        }

        $service = $booking->service;
        $month = (int) $request->get('month', Carbon::now()->month);
        $year = (int) $request->get('year', Carbon::now()->year);

        $calendarDates = $this->availabilityService->getAvailableDates($service, $month, $year);

        return view('user.booking.reschedule', compact('booking', 'service', 'calendarDates', 'month', 'year'));
    }

    /**
     * Process reschedule.
     */
    public function reschedule(string $bookingNo, Request $request): RedirectResponse
    {
        $request->validate([
            'new_date' => 'required|date|after_or_equal:today',
            'time_slot_id' => 'required|exists:time_slots,id',
            'therapist_id' => 'nullable|exists:therapists,id',
        ], [
            'new_date.required' => 'กรุณาเลือกวันนัดหมายใหม่',
            'time_slot_id.required' => 'กรุณาเลือกรอบเวลานัดหมายใหม่',
            'time_slot_id.exists' => 'ไม่พบรอบเวลาที่เลือก',
        ]);

        $booking = $this->authorizedBooking($bookingNo);

        try {
            $this->bookingService->rescheduleBooking(
                $booking,
                $request->new_date,
                (int) $request->time_slot_id,
                $request->therapist_id ? (int) $request->therapist_id : null
            );

            return redirect()
                ->route('booking.show', ['bookingNo' => $bookingNo])
                ->with('success', 'เลื่อนวันเวลานัดหมายเรียบร้อยแล้ว');
        } catch (InvalidArgumentException $e) {
            return back()->withInput()->withErrors(['reschedule' => $e->getMessage()]);
        }
    }

    /** A booking number alone is not sufficient to disclose or modify customer data. */
    private function authorizedBooking(string $bookingNo, array $relations = []): Booking
    {
        $booking = Booking::with($relations)->where('booking_no', $bookingNo)->firstOrFail();
        abort_unless(session('verified_phone_'.$bookingNo) === $booking->customer?->phone, 404);

        return $booking;
    }
}

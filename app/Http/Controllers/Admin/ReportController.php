<?php

namespace App\Http\Controllers\Admin;

use App\Exports\BookingsExport;
use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Service;
use App\Models\Therapist;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $filters = $request->validate([
            'date_from' => ['nullable', 'date'], 'date_to' => ['nullable', 'date', 'after_or_equal:date_from'],
            'status' => ['nullable', 'in:pending,confirmed,completed,cancelled,no_show'],
            'service_id' => ['nullable', 'exists:services,id'], 'therapist_id' => ['nullable', 'exists:therapists,id'],
        ]);
        $query = $this->filteredBookings($filters);
        $summaryQuery = clone $query;
        $summary = [
            'total' => (clone $summaryQuery)->count(),
            'cancelled' => (clone $summaryQuery)->where('status', Booking::STATUS_CANCELLED)->count(),
            'no_show' => (clone $summaryQuery)->where('status', Booking::STATUS_NO_SHOW)->count(),
            'completed' => (clone $summaryQuery)->where('status', Booking::STATUS_COMPLETED)->count(),
            'customers' => (clone $summaryQuery)->distinct('customer_id')->count('customer_id'),
        ];
        $bookings = $query->with(['customer', 'service', 'therapist', 'timeSlot'])->latest('booking_date')->paginate(30)->withQueryString();

        return view('admin.reports.index', ['bookings' => $bookings, 'summary' => $summary, 'filters' => $filters, 'services' => Service::orderBy('name_th')->get(), 'therapists' => Therapist::orderBy('first_name')->get()]);
    }

    public function excel(Request $request)
    {
        $filters = $request->validate(['date_from' => ['nullable', 'date'], 'date_to' => ['nullable', 'date'], 'status' => ['nullable', 'in:pending,confirmed,completed,cancelled,no_show'], 'service_id' => ['nullable', 'exists:services,id'], 'therapist_id' => ['nullable', 'exists:therapists,id']]);

        return Excel::download(new BookingsExport($filters), 'รายงานการจอง.xlsx');
    }

    public function pdf(Request $request)
    {
        $filters = $request->validate(['date_from' => ['nullable', 'date'], 'date_to' => ['nullable', 'date'], 'status' => ['nullable', 'in:pending,confirmed,completed,cancelled,no_show'], 'service_id' => ['nullable', 'exists:services,id'], 'therapist_id' => ['nullable', 'exists:therapists,id']]);
        $bookings = $this->filteredBookings($filters)->with(['customer', 'service', 'therapist', 'timeSlot'])->get();

        return Pdf::loadView('admin.reports.bookings', compact('bookings', 'filters'))->setPaper('a4', 'landscape')->download('รายงานการจอง.pdf');
    }

    private function filteredBookings(array $filters): Builder
    {
        return Booking::query()
            ->when($filters['date_from'] ?? null, fn ($q, $date) => $q->whereDate('booking_date', '>=', $date))
            ->when($filters['date_to'] ?? null, fn ($q, $date) => $q->whereDate('booking_date', '<=', $date))
            ->when($filters['status'] ?? null, fn ($q, $status) => $q->where('status', $status))
            ->when($filters['service_id'] ?? null, fn ($q, $id) => $q->where('service_id', $id))
            ->when($filters['therapist_id'] ?? null, fn ($q, $id) => $q->where('therapist_id', $id));
    }
}

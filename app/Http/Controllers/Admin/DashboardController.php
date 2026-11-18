<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Service;
use App\Models\Therapist;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $today = Carbon::today();
        $period = in_array($request->query('period'), ['daily', 'monthly', 'yearly'], true) ? $request->query('period') : 'daily';
        $todayStart = $today->copy()->startOfDay();
        $todayEnd = $today->copy()->endOfDay();
        $stats = Cache::remember('admin.dashboard.stats.'.$today->toDateString(), 60, function () use ($today, $todayStart, $todayEnd): array {
            $bookingStats = Booking::query()
                ->selectRaw('COUNT(*) as all_count')
                ->selectRaw('SUM(CASE WHEN booking_date BETWEEN ? AND ? AND status <> ? THEN 1 ELSE 0 END) as today_count', [$todayStart, $todayEnd, Booking::STATUS_CANCELLED])
                ->selectRaw('SUM(CASE WHEN booking_date BETWEEN ? AND ? THEN 1 ELSE 0 END) as month_count', [$today->copy()->startOfMonth(), $today->copy()->endOfMonth()])
                ->selectRaw('SUM(CASE WHEN booking_date BETWEEN ? AND ? THEN 1 ELSE 0 END) as year_count', [$today->copy()->startOfYear(), $today->copy()->endOfYear()])
                ->selectRaw('SUM(CASE WHEN status = ? THEN 1 ELSE 0 END) as pending_count', [Booking::STATUS_PENDING])
                ->selectRaw('SUM(CASE WHEN status = ? THEN 1 ELSE 0 END) as confirmed_count', [Booking::STATUS_CONFIRMED])
                ->selectRaw('SUM(CASE WHEN status = ? THEN 1 ELSE 0 END) as cancelled_count', [Booking::STATUS_CANCELLED])
                ->selectRaw('SUM(CASE WHEN status = ? THEN 1 ELSE 0 END) as completed_count', [Booking::STATUS_COMPLETED])
                ->selectRaw('SUM(CASE WHEN status = ? THEN 1 ELSE 0 END) as no_show_count', [Booking::STATUS_NO_SHOW])
                ->first();

            return [
                'all' => (int) $bookingStats->all_count,
                'today' => (int) $bookingStats->today_count,
                'month' => (int) $bookingStats->month_count,
                'year' => (int) $bookingStats->year_count,
                'pending' => (int) $bookingStats->pending_count,
                'confirmed' => (int) $bookingStats->confirmed_count,
                'cancelled' => (int) $bookingStats->cancelled_count,
                'completed' => (int) $bookingStats->completed_count,
                'no_show' => (int) $bookingStats->no_show_count,
                'services' => Service::where('is_active', true)->count(),
            ];
        });
        $popularServices = Service::query()
            ->select(['id', 'name_th'])
            ->withCount(['bookings' => fn ($query) => $query->where('booking_date', '>=', $today->copy()->subDays(30))])
            ->orderByDesc('bookings_count')
            ->take(5)
            ->get();
        $therapistStats = Therapist::query()
            ->select(['id', 'first_name', 'last_name', 'nickname'])
            ->withCount(['bookings as today_bookings_count' => fn ($q) => $q->whereBetween('booking_date', [$todayStart, $todayEnd])->whereNotIn('status', [Booking::STATUS_CANCELLED])])
            ->orderBy('first_name')
            ->get();
        $todayBookings = Booking::query()
            ->select(['id', 'booking_no', 'created_at', 'booking_date', 'time_slot_id', 'customer_id', 'service_id', 'therapist_id', 'status'])
            ->with([
                'customer:id,first_name,last_name,phone',
                'service:id,name_th,duration_minutes,price',
                'timeSlot:id,start_time,end_time',
                'therapist:id,first_name,last_name,nickname',
            ])
            ->whereBetween('booking_date', [$todayStart, $todayEnd])
            ->orderBy('time_slot_id')
            ->get();
        $chart = collect();
        if ($period === 'daily') {
            $startDate = $today->copy()->subDays(13);
            $dailyCounts = Booking::whereBetween('booking_date', [$startDate->toDateString(), $today->toDateString()])
                ->groupBy('booking_date')
                ->selectRaw('booking_date, count(*) as count')
                ->pluck('count', 'booking_date');

            for ($i = 13; $i >= 0; $i--) {
                $date = $today->copy()->subDays($i);
                $dateStr = $date->toDateString();
                $chart->push(['label' => $date->format('d/m'), 'count' => (int) ($dailyCounts[$dateStr] ?? 0)]);
            }
        } elseif ($period === 'monthly') {
            $firstMonth = $today->copy()->startOfMonth()->subMonths(11);
            $lastMonth = $today->copy()->endOfMonth();
            $monthExpression = match (DB::connection()->getDriverName()) {
                'sqlite' => "strftime('%Y-%m', booking_date)",
                'pgsql' => "TO_CHAR(booking_date, 'YYYY-MM')",
                default => "DATE_FORMAT(booking_date, '%Y-%m')",
            };
            $monthlyCounts = Booking::query()
                ->whereBetween('booking_date', [$firstMonth, $lastMonth])
                ->selectRaw("{$monthExpression} as period, COUNT(*) as count")
                ->groupByRaw($monthExpression)
                ->pluck('count', 'period');

            for ($i = 11; $i >= 0; $i--) {
                $date = $today->copy()->startOfMonth()->subMonths($i);
                $periodKey = $date->format('Y-m');
                $chart->push(['label' => $date->format('m/Y'), 'count' => (int) ($monthlyCounts[$periodKey] ?? 0)]);
            }
        } else {
            $firstYear = $today->year - 4;
            $yearExpression = match (DB::connection()->getDriverName()) {
                'sqlite' => "strftime('%Y', booking_date)",
                'pgsql' => 'CAST(EXTRACT(YEAR FROM booking_date) AS INTEGER)',
                default => 'YEAR(booking_date)',
            };
            $yearlyCounts = Booking::query()
                ->whereBetween('booking_date', [$today->copy()->setYear($firstYear)->startOfYear(), $today->copy()->endOfYear()])
                ->selectRaw("{$yearExpression} as period, COUNT(*) as count")
                ->groupByRaw($yearExpression)
                ->pluck('count', 'period');

            for ($i = 4; $i >= 0; $i--) {
                $year = $today->year - $i;
                $chart->push(['label' => (string) ($year + 543), 'count' => (int) ($yearlyCounts[(string) $year] ?? $yearlyCounts[$year] ?? 0)]);
            }
        }

        return view('admin.dashboard', compact('stats', 'popularServices', 'todayBookings', 'therapistStats', 'chart', 'period'));
    }
}

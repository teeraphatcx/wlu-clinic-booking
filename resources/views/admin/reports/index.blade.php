@extends('admin.layout')
@section('title', 'รายงานการจองคิว')

@section('content')
<div class="mb-6 flex flex-col justify-between gap-4 sm:flex-row sm:items-center">
    <div>
        <p class="text-xs font-semibold uppercase tracking-wider text-brand-600">Reports & Analytics</p>
        <h1 class="text-2xl font-bold text-slate-900 mt-0.5">รายงานสถิติการจองและบริการ</h1>
        <p class="mt-1 text-sm text-slate-500">กรองข้อมูลตามช่วงเวลา หัตถการ แพทย์แผนไทย และส่งออกเป็น Excel หรือ PDF</p>
    </div>
    <div class="flex flex-wrap items-center gap-2 print:hidden">
        <a href="{{ route('admin.reports.bookings.excel', request()->query()) }}" class="inline-flex items-center gap-1.5 rounded-xl bg-emerald-700 px-4 py-2.5 text-xs font-semibold text-white shadow-sm hover:bg-emerald-800 transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
            <span>ส่งออก Excel</span>
        </a>
        <a href="{{ route('admin.reports.bookings.pdf', request()->query()) }}" class="inline-flex items-center gap-1.5 rounded-xl bg-rose-700 px-4 py-2.5 text-xs font-semibold text-white shadow-sm hover:bg-rose-800 transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
            <span>ดาวน์โหลด PDF</span>
        </a>
        <button onclick="window.print()" class="inline-flex items-center gap-1.5 rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-xs font-semibold text-slate-700 shadow-sm hover:bg-slate-50 transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
            <span>พิมพ์</span>
        </button>
    </div>
</div>

<!-- Filters -->
<form method="get" action="{{ route('admin.reports.index') }}" class="mb-6 rounded-2xl bg-white p-5 shadow-sm border border-slate-100 print:hidden">
    <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-5 items-end">
        <div>
            <label class="mb-1 block text-xs font-semibold text-slate-600">ตั้งแต่วันที่</label>
            <input type="date" name="date_from" value="{{ request('date_from') }}" class="w-full rounded-xl border-slate-300 text-xs py-2 focus:border-brand-500 focus:ring-brand-200">
        </div>
        <div>
            <label class="mb-1 block text-xs font-semibold text-slate-600">ถึงวันที่</label>
            <input type="date" name="date_to" value="{{ request('date_to') }}" class="w-full rounded-xl border-slate-300 text-xs py-2 focus:border-brand-500 focus:ring-brand-200">
        </div>
        <div>
            <label class="mb-1 block text-xs font-semibold text-slate-600">สถานะ</label>
            <select name="status" class="w-full rounded-xl border-slate-300 text-xs py-2 focus:border-brand-500 focus:ring-brand-200">
                <option value="">ทุกสถานะ</option>
                @foreach(\App\Models\Booking::STATUSES as $key => $label)
                    <option value="{{ $key }}" @selected(request('status') === $key)>{{ $label }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="mb-1 block text-xs font-semibold text-slate-600">หัตถการ</label>
            <select name="service_id" class="w-full rounded-xl border-slate-300 text-xs py-2 focus:border-brand-500 focus:ring-brand-200">
                <option value="">ทั้งหมด</option>
                @foreach($services as $service)
                    <option value="{{ $service->id }}" @selected((string)request('service_id') === (string)$service->id)>{{ $service->name_th }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="mb-1 block text-xs font-semibold text-slate-600">หมอนวด / แพทย์</label>
            <select name="therapist_id" class="w-full rounded-xl border-slate-300 text-xs py-2 focus:border-brand-500 focus:ring-brand-200">
                <option value="">ทั้งหมด</option>
                @foreach($therapists as $therapist)
                    <option value="{{ $therapist->id }}" @selected((string)request('therapist_id') === (string)$therapist->id)>{{ $therapist->displayName }}</option>
                @endforeach
            </select>
        </div>
    </div>
    <div class="mt-4 flex flex-wrap items-center justify-end gap-2 pt-3 border-t border-slate-100">
        @if(request()->hasAny(['date_from', 'date_to', 'status', 'service_id', 'therapist_id']))
            <a href="{{ route('admin.reports.index') }}" class="rounded-xl border border-slate-200 bg-white px-4 py-2 text-xs font-semibold text-slate-600 hover:bg-slate-50">
                ล้างตัวกรอง
            </a>
        @endif
        <button type="submit" class="rounded-xl bg-brand-700 px-6 py-2 text-xs font-semibold text-white shadow-sm hover:bg-brand-800">
            แสดงรายงาน
        </button>
    </div>
</form>

<!-- Summary KPI Cards -->
<div class="mb-6 grid grid-cols-2 gap-3 sm:grid-cols-5">
    <div class="rounded-2xl border border-brand-100 bg-brand-50/50 p-4 shadow-sm">
        <p class="text-xs text-brand-700 font-semibold">จำนวนคิวทั้งหมด</p>
        <p class="mt-2 text-2xl font-bold text-brand-900">{{ number_format($summary['total']) }}</p>
    </div>
    <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
        <p class="text-xs text-slate-500 font-semibold">ผู้รับบริการที่ไม่ซ้ำ</p>
        <p class="mt-2 text-2xl font-bold text-slate-800">{{ number_format($summary['customers']) }}</p>
    </div>
    <div class="rounded-2xl border border-emerald-100 bg-emerald-50/50 p-4 shadow-sm">
        <p class="text-xs text-emerald-700 font-semibold">เข้ารับบริการแล้ว</p>
        <p class="mt-2 text-2xl font-bold text-emerald-900">{{ number_format($summary['completed']) }}</p>
    </div>
    <div class="rounded-2xl border border-rose-100 bg-rose-50/50 p-4 shadow-sm">
        <p class="text-xs text-rose-700 font-semibold">ยกเลิก</p>
        <p class="mt-2 text-2xl font-bold text-rose-900">{{ number_format($summary['cancelled']) }}</p>
    </div>
    <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
        <p class="text-xs text-slate-500 font-semibold">ไม่มาตามนัด</p>
        <p class="mt-2 text-2xl font-bold text-slate-800">{{ number_format($summary['no_show']) }}</p>
    </div>
</div>

<!-- Report Table -->
<section class="rounded-2xl bg-white shadow-sm border border-slate-100 overflow-hidden">
    <div class="px-5 py-4 border-b border-slate-100">
        <h2 class="font-bold text-slate-900 text-base">รายการคิวที่ตรวจพบ</h2>
        <p class="text-xs text-slate-500 mt-0.5">{{ number_format($bookings->total()) }} รายการตามเงื่อนไขตัวกรอง</p>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full min-w-[850px] text-left text-sm">
            <thead class="bg-slate-50 text-slate-600">
                <tr>
                    <th class="px-5 py-3">รหัสการจอง</th>
                    <th>วันเวลานัดหมาย</th>
                    <th>ผู้รับบริการ</th>
                    <th>หัตถการ</th>
                    <th>หมอนวด</th>
                    <th class="px-5">สถานะ</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $statusBadgeMap = [
                        'pending' => 'bg-amber-50 text-amber-800 border-amber-200',
                        'confirmed' => 'bg-brand-50 text-brand-800 border-brand-200',
                        'completed' => 'bg-emerald-50 text-emerald-800 border-emerald-200',
                        'cancelled' => 'bg-rose-50 text-rose-800 border-rose-200',
                        'no_show' => 'bg-slate-100 text-slate-700 border-slate-200',
                    ];
                @endphp
                @forelse($bookings as $booking)
                    <tr class="border-t border-slate-100 hover:bg-slate-50/70 transition-colors">
                        <td class="px-5 py-3.5 whitespace-nowrap">
                            <a href="{{ route('admin.bookings.history', $booking) }}" class="font-mono font-bold text-brand-700 hover:underline">
                                {{ $booking->booking_no }}
                            </a>
                        </td>
                        <td class="whitespace-nowrap py-3.5">
                            <span class="font-semibold text-slate-800">{{ $booking->booking_date?->format('d/m/Y') }}</span>
                            <span class="block text-xs text-slate-500">⏱ {{ substr($booking->timeSlot?->start_time ?? '', 0, 5) }}</span>
                        </td>
                        <td class="py-3.5">
                            <p class="font-medium text-slate-900">{{ $booking->customer?->full_name }}</p>
                            <span class="text-xs text-slate-500">{{ $booking->customer?->phone }}</span>
                        </td>
                        <td class="py-3.5">
                            <p class="font-medium text-slate-800">{{ $booking->service?->name_th }}</p>
                            <span class="text-xs text-slate-500">฿{{ number_format($booking->service?->price, 0) }}</span>
                        </td>
                        <td class="py-3.5 text-xs text-slate-700">
                            {{ $booking->therapist?->displayName ?? 'จัดให้อัตโนมัติ' }}
                        </td>
                        <td class="px-5 py-3.5 whitespace-nowrap">
                            <span class="inline-block rounded-full border px-2.5 py-0.5 text-xs font-semibold {{ $statusBadgeMap[$booking->status] ?? 'bg-slate-100 text-slate-700 border-slate-200' }}">
                                {{ \App\Models\Booking::STATUSES[$booking->status] ?? $booking->status }}
                            </span>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="p-10 text-center text-slate-400 text-sm">
                            ไม่พบข้อมูลการจองตามเงื่อนไขที่เลือก
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($bookings->hasPages())
        <div class="border-t border-slate-100 px-5 py-4 print:hidden">
            {{ $bookings->links() }}
        </div>
    @endif
</section>
@endsection

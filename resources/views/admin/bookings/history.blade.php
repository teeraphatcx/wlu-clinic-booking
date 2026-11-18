@extends('admin.layout')
@section('title', 'ประวัติการจอง ' . $booking->booking_no)

@section('content')
<div class="mb-6">
    <a href="{{ route('admin.bookings.index') }}" class="inline-flex items-center gap-1.5 text-sm font-semibold text-brand-700 hover:text-brand-900 mb-3">
        ← กลับไปหน้าจัดการคิว
    </a>
    <div class="flex flex-wrap items-center justify-between gap-3">
        <div>
            <h1 class="text-2xl font-bold text-slate-900">ประวัติและข้อมูลการจอง</h1>
            <p class="mt-0.5 text-sm text-slate-500 font-mono font-semibold">{{ $booking->booking_no }}</p>
        </div>
        @php
            $statusBadgeMap = [
                'pending' => 'bg-amber-50 text-amber-800 border-amber-200',
                'confirmed' => 'bg-brand-50 text-brand-800 border-brand-200',
                'completed' => 'bg-emerald-50 text-emerald-800 border-emerald-200',
                'cancelled' => 'bg-rose-50 text-rose-800 border-rose-200',
                'no_show' => 'bg-slate-100 text-slate-700 border-slate-200',
            ];
        @endphp
        <span class="inline-block rounded-full border px-3 py-1 text-sm font-semibold {{ $statusBadgeMap[$booking->status] ?? 'bg-slate-100 text-slate-700 border-slate-200' }}">
            {{ \App\Models\Booking::STATUSES[$booking->status] ?? $booking->status }}
        </span>
    </div>
</div>

<!-- Current Booking Summary Card -->
<div class="mb-6 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
    <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
        <p class="text-xs font-semibold text-slate-500">ผู้รับบริการ</p>
        <p class="mt-1 text-base font-bold text-slate-900">{{ $booking->customer?->full_name }}</p>
        <p class="mt-0.5 text-xs text-brand-700">
            <a href="tel:{{ $booking->customer?->phone }}">{{ $booking->customer?->phone }}</a>
        </p>
        @if($booking->customer?->hn)
            <span class="mt-2 inline-block text-[11px] bg-slate-100 text-slate-600 px-2 py-0.5 rounded">
                HN: {{ $booking->customer->hn }}
            </span>
        @endif
    </div>

    <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
        <p class="text-xs font-semibold text-slate-500">หัตถการที่จอง</p>
        <p class="mt-1 text-base font-bold text-slate-900">{{ $booking->service?->name_th }}</p>
        <p class="mt-0.5 text-xs text-slate-500">
            ฿{{ number_format($booking->service?->price, 0) }} · {{ $booking->service?->duration_minutes }} นาที
        </p>
    </div>

    <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
        <p class="text-xs font-semibold text-slate-500">วันเวลานัดหมาย</p>
        <p class="mt-1 text-base font-bold text-slate-900">{{ $booking->booking_date?->format('d/m/Y') }}</p>
        <p class="mt-0.5 text-xs font-medium text-brand-700">
            ⏱ {{ substr($booking->timeSlot?->start_time ?? '', 0, 5) }} - {{ substr($booking->timeSlot?->end_time ?? '', 0, 5) }}
        </p>
    </div>

    <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
        <p class="text-xs font-semibold text-slate-500">แพทย์แผนไทย / หมอนวด</p>
        <p class="mt-1 text-base font-bold text-slate-900">{{ $booking->therapist?->displayName ?? 'ระบบจัดให้อัตโนมัติ' }}</p>
        @if($booking->notes)
            <p class="mt-2 text-xs text-slate-600 italic bg-slate-50 p-1.5 rounded">
                "{{ $booking->notes }}"
            </p>
        @endif
    </div>
</div>

<!-- Change Log & History Table -->
<section class="rounded-2xl bg-white shadow-sm border border-slate-100 overflow-hidden">
    <div class="px-5 py-4 border-b border-slate-100">
        <h2 class="font-bold text-slate-900 text-base">บันทึกประวัติการเปลี่ยนแปลง (Audit Log)</h2>
        <p class="text-xs text-slate-500 mt-0.5">ประวัติการสร้าง เลื่อนนัด และเปลี่ยนสถานะของรายการนี้</p>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full min-w-[700px] text-left text-sm">
            <thead class="bg-slate-50 text-slate-600">
                <tr>
                    <th class="px-5 py-3">วันเวลา</th>
                    <th>การกระทำ</th>
                    <th>สถานะ</th>
                    <th>รายละเอียด / หมายเหตุ</th>
                    <th class="px-5">ผู้ดำเนินการ</th>
                </tr>
            </thead>
            <tbody>
                @forelse($booking->histories->sortByDesc('created_at') as $history)
                    <tr class="border-t border-slate-100 align-top hover:bg-slate-50/70 transition-colors">
                        <td class="px-5 py-3.5 whitespace-nowrap text-xs font-medium text-slate-600">
                            {{ $history->created_at?->format('d/m/Y H:i:s') }}
                        </td>
                        <td class="py-3.5">
                            @php
                                $actionMap = [
                                    'created' => 'สร้างการจอง',
                                    'status_changed' => 'เปลี่ยนสถานะ',
                                    'rescheduled' => 'เลื่อนวันเวลานัด',
                                    'cancelled' => 'ยกเลิกการจอง',
                                    'no_show' => 'ปรับเป็นไม่มาตามนัด',
                                ];
                            @endphp
                            <span class="font-semibold text-slate-800">
                                {{ $actionMap[$history->action] ?? $history->action }}
                            </span>
                        </td>
                        <td class="py-3.5 whitespace-nowrap text-xs">
                            @if($history->old_status && $history->old_status !== $history->new_status)
                                <span class="text-slate-400 line-through mr-1">
                                    {{ \App\Models\Booking::STATUSES[$history->old_status] ?? $history->old_status }}
                                </span>
                                →
                            @endif
                            <span class="font-semibold text-slate-800 ml-1">
                                {{ \App\Models\Booking::STATUSES[$history->new_status] ?? $history->new_status }}
                            </span>
                        </td>
                        <td class="py-3.5 text-xs text-slate-700">
                            <p>{{ $history->notes ?: '—' }}</p>
                            @if($history->new_values && is_array($history->new_values))
                                <div class="mt-1.5 flex flex-wrap gap-1.5 text-[11px] text-slate-600 font-mono bg-slate-50 p-2 rounded-lg border border-slate-200">
                                    @foreach($history->new_values as $key => $val)
                                        <span class="bg-white px-2 py-0.5 rounded border border-slate-200">
                                            <strong>{{ $key }}:</strong> {{ is_array($val) ? json_encode($val) : $val }}
                                        </span>
                                    @endforeach
                                </div>
                            @endif
                        </td>
                        <td class="px-5 py-3.5 text-xs text-slate-600 whitespace-nowrap">
                            <span class="inline-flex items-center gap-1 font-medium text-slate-800">
                                👤 {{ $history->admin?->name ?? 'ผู้รับบริการ / ระบบอัตโนมัติ' }}
                            </span>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="p-8 text-center text-slate-400 text-sm">
                            ไม่มีประวัติการเปลี่ยนแปลง
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</section>
@endsection

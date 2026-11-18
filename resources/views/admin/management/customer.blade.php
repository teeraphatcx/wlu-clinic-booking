@extends('admin.layout')

@section('title', 'ข้อมูลและประวัติผู้รับบริการ: ' . $customer->full_name)

@section('content')
<div class="mb-6 flex flex-wrap items-center justify-between gap-4">
    <div class="flex items-center gap-3">
        <a href="{{ route('admin.customers.index') }}" class="inline-flex h-9 w-9 items-center justify-center rounded-xl border border-slate-200 bg-white text-slate-600 hover:bg-slate-50 transition shadow-sm" title="กลับไปหน้ารายชื่อผู้รับบริการ">
            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
        </a>
        <div>
            <p class="text-xs font-semibold uppercase tracking-wider text-brand-600">Customer Profile</p>
            <h1 class="text-2xl font-bold text-slate-800">{{ $customer->full_name }}</h1>
        </div>
    </div>
    <div class="flex flex-wrap items-center gap-2">
        <a href="{{ route('admin.bookings.index', ['search' => $customer->phone]) }}" class="inline-flex items-center gap-1.5 rounded-xl border border-brand-200 bg-brand-50 px-3.5 py-2 text-xs font-semibold text-brand-700 hover:bg-brand-100 transition">
            <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
            จองคิวให้ผู้รับบริการนี้
        </a>
        <a href="{{ route('admin.customers.index', ['search' => $customer->phone]) }}" class="inline-flex items-center gap-1.5 rounded-xl border border-slate-200 bg-white px-3.5 py-2 text-xs font-semibold text-slate-700 hover:bg-slate-50 transition shadow-sm">
            <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
            แก้ไขข้อมูล
        </a>
        <form method="post" action="{{ route('admin.customers.status', $customer) }}" onsubmit="return confirm('ยืนยันเปลี่ยนสถานะผู้รับบริการรายนี้หรือไม่?')">
            @csrf
            @method('PATCH')
            <button type="submit" class="inline-flex items-center gap-1.5 rounded-xl border px-3.5 py-2 text-xs font-semibold transition {{ $customer->is_active ? 'border-rose-200 bg-rose-50 text-rose-700 hover:bg-rose-100' : 'border-emerald-200 bg-emerald-50 text-emerald-700 hover:bg-emerald-100' }}">
                {{ $customer->is_active ? 'ระงับการใช้งาน' : 'เปิดใช้งาน' }}
            </button>
        </form>
    </div>
</div>

{{-- ข้อมูลโปรไฟล์ผู้รับบริการ --}}
<div class="mb-6 grid gap-6 lg:grid-cols-3">
    <div class="rounded-2xl border border-slate-100 bg-white p-6 shadow-sm lg:col-span-1">
        <div class="text-center">
            <div class="mx-auto grid h-20 w-20 place-items-center rounded-2xl bg-gradient-to-br from-brand-700 to-brand-900 text-2xl font-bold text-white shadow-md shadow-brand-900/20">
                {{ mb_substr($customer->first_name, 0, 1) }}
            </div>
            <h2 class="mt-4 text-lg font-bold text-slate-800">{{ $customer->full_name }}</h2>
            <div class="mt-1 flex items-center justify-center gap-2">
                @if($customer->hn)
                    <span class="rounded-md bg-brand-50 px-2 py-0.5 text-xs font-mono font-semibold text-brand-700 border border-brand-100">HN: {{ $customer->hn }}</span>
                @else
                    <span class="rounded-md bg-slate-100 px-2 py-0.5 text-xs text-slate-500">ไม่มีรหัส HN</span>
                @endif
                <span class="rounded-full px-2.5 py-0.5 text-xs font-semibold {{ $customer->is_active ? 'bg-emerald-50 text-emerald-700 border border-emerald-100' : 'bg-rose-50 text-rose-700 border border-rose-100' }}">
                    {{ $customer->is_active ? 'เปิดใช้งานปกติ' : 'ระงับการใช้งาน' }}
                </span>
            </div>
        </div>

        <div class="mt-6 border-t border-slate-100 pt-5 space-y-3.5 text-sm">
            <div class="flex items-center justify-between">
                <span class="text-xs text-slate-500">เบอร์โทรศัพท์</span>
                <a href="tel:{{ $customer->phone }}" class="font-medium text-brand-700 hover:underline flex items-center gap-1">
                    <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                    {{ $customer->phone }}
                </a>
            </div>
            <div class="flex items-center justify-between">
                <span class="text-xs text-slate-500">อีเมล</span>
                <span class="font-medium text-slate-700">{{ $customer->email ?: '—' }}</span>
            </div>
            <div class="flex items-center justify-between">
                <span class="text-xs text-slate-500">เพศ</span>
                <span class="font-medium text-slate-700">
                    {{ match($customer->gender) { 'male' => 'ชาย', 'female' => 'หญิง', default => 'ไม่ระบุ' } }}
                </span>
            </div>
            <div class="flex items-center justify-between">
                <span class="text-xs text-slate-500">วันเกิด / อายุ</span>
                <span class="font-medium text-slate-700">
                    @if($customer->birthdate)
                        {{ $customer->birthdate->format('d/m/Y') }} ({{ $customer->birthdate->age }} ปี)
                    @else
                        —
                    @endif
                </span>
            </div>
            <div class="flex items-start justify-between gap-2">
                <span class="text-xs text-slate-500">ที่อยู่</span>
                <span class="font-medium text-slate-700 text-right text-xs max-w-[200px]">{{ $customer->address ?: '—' }}</span>
            </div>
            <div class="flex items-center justify-between">
                <span class="text-xs text-slate-500">ยินยอม PDPA</span>
                <span class="text-xs {{ $customer->pdpa_accepted_at ? 'text-emerald-700' : 'text-slate-400' }}">
                    {{ $customer->pdpa_accepted_at ? 'ยินยอมแล้ว (' . $customer->pdpa_accepted_at->format('d/m/Y') . ')' : 'ยังไม่ยืนยัน' }}
                </span>
            </div>
            <div class="flex items-center justify-between">
                <span class="text-xs text-slate-500">ลงทะเบียนเมื่อ</span>
                <span class="text-xs text-slate-600">{{ $customer->created_at?->format('d/m/Y H:i น.') ?? '—' }}</span>
            </div>
        </div>

        @if($customer->notes)
            <div class="mt-5 rounded-xl border border-amber-200 bg-amber-50/70 p-3.5">
                <p class="text-xs font-semibold text-amber-900 flex items-center gap-1.5">
                    <svg class="h-4 w-4 text-amber-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    บันทึก / อาการเบื้องต้น
                </p>
                <p class="mt-1 text-xs text-amber-800 leading-relaxed">{{ $customer->notes }}</p>
            </div>
        @endif
    </div>

    {{-- สถิติการใช้บริการและการวิเคราะห์ --}}
    <div class="space-y-6 lg:col-span-2">
        <div class="grid gap-3 sm:grid-cols-4">
            <div class="rounded-2xl border border-slate-100 bg-white p-4 shadow-sm text-center">
                <p class="text-xs text-slate-500 font-medium">การจองทั้งหมด</p>
                <p class="mt-1 text-2xl font-bold text-slate-800">{{ number_format((int) $bookingStats->total_count) }}</p>
                <p class="mt-0.5 text-[11px] text-slate-400">ครั้ง</p>
            </div>
            <div class="rounded-2xl border border-emerald-100 bg-emerald-50/50 p-4 shadow-sm text-center">
                <p class="text-xs font-medium text-emerald-700">สำเร็จแล้ว</p>
                <p class="mt-1 text-2xl font-bold text-emerald-800">{{ number_format((int) $bookingStats->completed_count) }}</p>
                <p class="mt-0.5 text-[11px] text-emerald-600">ครั้ง</p>
            </div>
            <div class="rounded-2xl border border-brand-100 bg-brand-50/50 p-4 shadow-sm text-center">
                <p class="text-xs font-medium text-brand-700">คิวที่กำลังรอ/ยืนยัน</p>
                <p class="mt-1 text-2xl font-bold text-brand-800">{{ number_format((int) $bookingStats->active_count) }}</p>
                <p class="mt-0.5 text-[11px] text-brand-600">ครั้ง</p>
            </div>
            <div class="rounded-2xl border border-rose-100 bg-rose-50/50 p-4 shadow-sm text-center">
                <p class="text-xs font-medium text-rose-700">ยกเลิก / ไม่มา</p>
                <p class="mt-1 text-2xl font-bold text-rose-800">{{ number_format((int) $bookingStats->cancelled_count) }}</p>
                <p class="mt-0.5 text-[11px] text-rose-600">ครั้ง</p>
            </div>
        </div>

        {{-- ตารางประวัติการจองทั้งหมดของผู้รับบริการ --}}
        <section class="overflow-hidden rounded-2xl bg-white shadow-sm border border-slate-100">
            <div class="flex flex-wrap items-center justify-between gap-3 border-b border-slate-100 px-6 py-4">
                <div>
                    <h3 class="font-bold text-slate-800">ประวัติการจองคิวและการรักษา</h3>
                    <p class="text-xs text-slate-500">เรียงตามวันที่จองล่าสุด</p>
                </div>
                <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-600">
                    {{ number_format((int) $bookingStats->total_count) }} รายการ
                </span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full min-w-[700px] text-left text-sm">
                    <thead class="bg-slate-50 text-xs font-semibold text-slate-600">
                        <tr>
                            <th class="px-6 py-3.5">รหัสจอง</th>
                            <th class="px-4 py-3.5">วันเวลานัด</th>
                            <th class="px-4 py-3.5">หัตถการ</th>
                            <th class="px-4 py-3.5">หมอนวด/แพทย์</th>
                            <th class="px-4 py-3.5">สถานะ</th>
                            <th class="px-6 py-3.5 text-right">รายละเอียด</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($bookings as $booking)
                            <tr class="hover:bg-slate-50/70 transition">
                                <td class="px-6 py-4">
                                    <a href="{{ route('admin.bookings.history', $booking) }}" class="font-mono font-bold text-brand-700 hover:text-brand-900 hover:underline">
                                        {{ $booking->booking_no }}
                                    </a>
                                </td>
                                <td class="px-4 py-4 whitespace-nowrap text-xs">
                                    <p class="font-medium text-slate-800">{{ $booking->booking_date?->format('d/m/Y') }}</p>
                                    <p class="text-slate-500 font-mono">{{ substr($booking->timeSlot?->start_time ?? '', 0, 5) }} - {{ substr($booking->timeSlot?->end_time ?? '', 0, 5) }} น.</p>
                                </td>
                                <td class="px-4 py-4 text-xs">
                                    <p class="font-medium text-slate-800">{{ $booking->service?->name_th }}</p>
                                    <p class="text-slate-400 text-[11px]">{{ $booking->service?->duration_minutes }} นาที · {{ number_format($booking->service?->price ?? 0) }} บาท</p>
                                </td>
                                <td class="px-4 py-4 text-xs text-slate-600">
                                    @if($booking->therapist)
                                        <span class="inline-flex items-center gap-1 text-slate-800 font-medium">
                                            <span class="h-1.5 w-1.5 rounded-full bg-brand-600"></span>
                                            {{ $booking->therapist->displayName }}
                                        </span>
                                    @else
                                        <span class="text-slate-400">จัดให้อัตโนมัติ</span>
                                    @endif
                                </td>
                                <td class="px-4 py-4">
                                    @php
                                        $badgeClasses = match($booking->status) {
                                            'completed' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                                            'confirmed' => 'bg-blue-50 text-blue-700 border-blue-200',
                                            'pending' => 'bg-amber-50 text-amber-700 border-amber-200',
                                            'cancelled', 'no_show' => 'bg-rose-50 text-rose-700 border-rose-200',
                                            default => 'bg-slate-50 text-slate-700 border-slate-200',
                                        };
                                    @endphp
                                    <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-semibold border {{ $badgeClasses }}">
                                        {{ $booking::STATUSES[$booking->status] ?? $booking->status }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <a href="{{ route('admin.bookings.history', $booking) }}" class="inline-flex items-center gap-1 text-xs font-semibold text-brand-700 hover:text-brand-900 hover:underline">
                                        บันทึกการปรับปรุง
                                        <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-12 text-center text-sm text-slate-500">
                                    ยังไม่มีประวัติการจองคิวสำหรับผู้รับบริการนี้
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($bookings->hasPages())
                <div class="border-t border-slate-100 px-6 py-4">{{ $bookings->links() }}</div>
            @endif
        </section>
    </div>
</div>
@endsection

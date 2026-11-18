@extends('admin.layout')
@section('title', 'จัดการคิว')

@section('content')
<div class="mb-6 flex flex-col justify-between gap-4 sm:flex-row sm:items-center">
    <div>
        <p class="text-xs font-semibold uppercase tracking-wider text-brand-600">Booking Management</p>
        <h1 class="text-2xl font-bold text-slate-900 mt-0.5">จัดการคิวรับบริการ</h1>
        <p class="mt-1 text-sm text-slate-500">ตรวจสอบ แก้ไขสถานะ เลื่อนนัด และสร้างคิวใหม่โดยเจ้าหน้าที่</p>
    </div>
</div>

<!-- Search & Filter Bar -->
<section class="mb-6 rounded-2xl bg-white p-5 shadow-sm border border-slate-100">
    <form method="get" action="{{ route('admin.bookings.index') }}" class="grid gap-3 sm:grid-cols-2 lg:grid-cols-[1fr_180px_180px_auto_auto] items-end">
        <div>
            <label class="mb-1 block text-xs font-semibold text-slate-600">ค้นหา</label>
            <input name="search" value="{{ request('search') }}" placeholder="ค้นหารหัสคิว, ชื่อ, หรือเบอร์โทร" class="w-full rounded-xl border-slate-300 text-sm focus:border-brand-500 focus:ring-brand-200">
        </div>
        <div>
            <label class="mb-1 block text-xs font-semibold text-slate-600">วันที่นัดหมาย</label>
            <input type="date" name="date" value="{{ request('date') }}" class="w-full rounded-xl border-slate-300 text-sm focus:border-brand-500 focus:ring-brand-200">
        </div>
        <div>
            <label class="mb-1 block text-xs font-semibold text-slate-600">สถานะ</label>
            <select name="status" class="w-full rounded-xl border-slate-300 text-sm focus:border-brand-500 focus:ring-brand-200">
                <option value="">ทุกสถานะ</option>
                @foreach(\App\Models\Booking::STATUSES as $key => $label)
                    <option value="{{ $key }}" @selected(request('status') === $key)>{{ $label }}</option>
                @endforeach
            </select>
        </div>
        <button type="submit" class="rounded-xl bg-brand-700 px-5 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-brand-800 transition-colors">
            ค้นหา
        </button>
        @if(request()->hasAny(['search', 'date', 'status']))
            <a href="{{ route('admin.bookings.index') }}" class="rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-center text-sm font-semibold text-slate-600 hover:bg-slate-50 transition-colors">
                ล้างตัวกรอง
            </a>
        @endif
    </form>
</section>

<!-- Add Booking Accordion -->
<details class="group mb-6 rounded-2xl bg-white border border-slate-200 shadow-sm overflow-hidden" {{ $errors->has('booking') ? 'open' : '' }}>
    <summary class="flex cursor-pointer items-center justify-between p-5 font-semibold text-slate-800 hover:bg-slate-50 transition-colors select-none">
        <div class="flex items-center gap-3">
            <span class="grid h-8 w-8 place-items-center rounded-xl bg-brand-50 text-brand-700 font-bold text-lg">＋</span>
            <div>
                <p class="font-bold text-slate-900 text-base">เพิ่มรายการจองคิวใหม่โดยเจ้าหน้าที่</p>
                <p class="text-xs text-slate-500 font-normal">สำหรับผู้รับบริการที่มาจองที่คลินิกหรือโทรศัพท์เข้ามาจอง</p>
            </div>
        </div>
        <span class="text-xs text-brand-700 font-semibold group-open:rotate-180 transition-transform duration-200">▼</span>
    </summary>

    <div class="border-t border-slate-100 p-5 sm:p-6 bg-slate-50/50">
        <form method="post" action="{{ route('admin.bookings.store') }}" class="space-y-4">
            @csrf
            <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                <div class="lg:col-span-2">
                    <label class="mb-1 block text-xs font-semibold text-slate-700">หัตถการ <span class="text-rose-500">*</span></label>
                    <select name="service_id" required class="w-full rounded-xl border-slate-300 text-sm focus:border-brand-500 focus:ring-brand-200">
                        <option value="">-- เลือกบริการหัตถการ --</option>
                        @foreach($services as $service)
                            <option value="{{ $service->id }}" @selected(old('service_id') == $service->id)>
                                {{ $service->name_th }} ({{ $service->duration_minutes }} นาที · ฿{{ number_format($service->price, 0) }})
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="mb-1 block text-xs font-semibold text-slate-700">วันที่นัดหมาย <span class="text-rose-500">*</span></label>
                    <input type="date" name="booking_date" min="{{ now()->toDateString() }}" value="{{ old('booking_date', now()->toDateString()) }}" required class="w-full rounded-xl border-slate-300 text-sm focus:border-brand-500 focus:ring-brand-200">
                </div>
                <div>
                    <label class="mb-1 block text-xs font-semibold text-slate-700">รอบเวลา <span class="text-rose-500">*</span></label>
                    <select name="time_slot_id" required class="w-full rounded-xl border-slate-300 text-sm focus:border-brand-500 focus:ring-brand-200">
                        <option value="">-- เลือกรอบเวลา --</option>
                        @foreach($slots as $slot)
                            <option value="{{ $slot->id }}" @selected(old('time_slot_id') == $slot->id)>
                                {{ substr($slot->start_time, 0, 5) }} - {{ substr($slot->end_time, 0, 5) }} ({{ $slot->label_th }})
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                <div>
                    <label class="mb-1 block text-xs font-semibold text-slate-700">แพทย์แผนไทย/หมอนวด</label>
                    <select name="therapist_id" class="w-full rounded-xl border-slate-300 text-sm focus:border-brand-500 focus:ring-brand-200">
                        <option value="">ไม่ระบุ / ระบบจัดให้อัตโนมัติ</option>
                        @foreach($therapists as $therapist)
                            <option value="{{ $therapist->id }}" @selected(old('therapist_id') == $therapist->id)>
                                {{ $therapist->displayName }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="mb-1 block text-xs font-semibold text-slate-700">ชื่อ <span class="text-rose-500">*</span></label>
                    <input name="first_name" value="{{ old('first_name') }}" required placeholder="ชื่อจริงผู้รับบริการ" class="w-full rounded-xl border-slate-300 text-sm focus:border-brand-500 focus:ring-brand-200">
                </div>
                <div>
                    <label class="mb-1 block text-xs font-semibold text-slate-700">นามสกุล <span class="text-rose-500">*</span></label>
                    <input name="last_name" value="{{ old('last_name') }}" required placeholder="นามสกุล" class="w-full rounded-xl border-slate-300 text-sm focus:border-brand-500 focus:ring-brand-200">
                </div>
                <div>
                    <label class="mb-1 block text-xs font-semibold text-slate-700">เบอร์โทรศัพท์ <span class="text-rose-500">*</span></label>
                    <input name="phone" value="{{ old('phone') }}" required placeholder="เช่น 0812345678" class="w-full rounded-xl border-slate-300 text-sm focus:border-brand-500 focus:ring-brand-200">
                </div>
            </div>

            <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                <div>
                    <label class="mb-1 block text-xs font-semibold text-slate-700">อีเมล (ถ้ามี)</label>
                    <input name="email" type="email" value="{{ old('email') }}" placeholder="example@email.com" class="w-full rounded-xl border-slate-300 text-sm focus:border-brand-500 focus:ring-brand-200">
                </div>
                <div class="sm:col-span-2 lg:col-span-3">
                    <label class="mb-1 block text-xs font-semibold text-slate-700">หมายเหตุ / ข้อมูลเพิ่มเติม</label>
                    <input name="notes" value="{{ old('notes') }}" placeholder="อาการเบื้องต้น หรือข้อจำกัดสุขภาพ" class="w-full rounded-xl border-slate-300 text-sm focus:border-brand-500 focus:ring-brand-200">
                </div>
            </div>

            <div class="pt-2 flex justify-end">
                <button type="submit" class="rounded-xl bg-brand-700 px-6 py-2.5 text-sm font-semibold text-white shadow-md hover:bg-brand-800 transition-colors">
                    บันทึกและสร้างคิวรับบริการ
                </button>
            </div>
        </form>
    </div>
</details>

<!-- Bookings Table -->
<section class="rounded-2xl bg-white shadow-sm border border-slate-100 overflow-hidden">
    <div class="flex flex-wrap items-center justify-between gap-3 px-5 py-4 border-b border-slate-100">
        <div>
            <h2 class="font-bold text-slate-900 text-base">รายการคิวทั้งหมด</h2>
            <p class="text-xs text-slate-500 mt-0.5">พบ {{ number_format($bookings->total()) }} รายการ</p>
        </div>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full min-w-[950px] text-left text-sm">
            <thead class="bg-slate-50 text-slate-600">
                <tr>
                    <th class="px-5 py-3.5">รหัสการจอง</th>
                    <th>วันเวลานัดหมาย</th>
                    <th>ผู้รับบริการ</th>
                    <th>หัตถการ</th>
                    <th>หมอนวด</th>
                    <th>สถานะ</th>
                    <th class="px-5 text-right">การจัดการ</th>
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
                    <tr class="border-t border-slate-100 align-top hover:bg-slate-50/70 transition-colors">
                        <td class="px-5 py-4 whitespace-nowrap">
                            <a href="{{ route('admin.bookings.history', $booking) }}" class="font-mono font-bold text-brand-700 hover:text-brand-900 hover:underline">
                                {{ $booking->booking_no }}
                            </a>
                            <p class="text-[11px] text-slate-400 mt-0.5">จองเมื่อ {{ $booking->created_at?->format('d/m/Y H:i') }}</p>
                        </td>
                        <td class="whitespace-nowrap py-4">
                            <p class="font-semibold text-slate-800">{{ $booking->booking_date?->format('d/m/Y') }}</p>
                            <span class="inline-block mt-0.5 font-medium text-xs text-slate-600 bg-slate-100 px-2 py-0.5 rounded">
                                ⏱ {{ substr($booking->timeSlot?->start_time ?? '', 0, 5) }} - {{ substr($booking->timeSlot?->end_time ?? '', 0, 5) }}
                            </span>
                        </td>
                        <td class="py-4">
                            <p class="font-medium text-slate-900">{{ $booking->customer?->full_name }}</p>
                            @if($booking->customer?->phone)
                                <a href="tel:{{ $booking->customer->phone }}" class="text-xs text-brand-700 hover:underline">
                                    {{ $booking->customer->phone }}
                                </a>
                            @endif
                        </td>
                        <td class="py-4">
                            <p class="font-medium text-slate-800">{{ $booking->service?->name_th }}</p>
                            <span class="text-xs text-slate-500">฿{{ number_format($booking->service?->price, 0) }} · {{ $booking->service?->duration_minutes }} นาที</span>
                        </td>
                        <td class="py-4">
                            <span class="text-xs font-medium text-slate-700">
                                {{ $booking->therapist?->displayName ?? 'ระบบจัดให้อัตโนมัติ' }}
                            </span>
                        </td>
                        <td class="py-4">
                            <span class="inline-block rounded-full border px-2.5 py-0.5 text-xs font-semibold {{ $statusBadgeMap[$booking->status] ?? 'bg-slate-100 text-slate-700 border-slate-200' }}">
                                {{ \App\Models\Booking::STATUSES[$booking->status] ?? $booking->status }}
                            </span>
                        </td>
                        <td class="px-5 py-4 text-right space-y-2">
                            <!-- Quick Status Update -->
                            <form method="post" action="{{ route('admin.bookings.status', $booking) }}" class="inline-flex items-center gap-1">
                                @csrf
                                @method('PATCH')
                                <select name="status" class="rounded-lg border-slate-300 py-1 px-2 text-xs font-medium focus:border-brand-500 focus:ring-brand-200">
                                    @foreach(\App\Models\Booking::STATUSES as $key => $label)
                                        <option value="{{ $key }}" @selected($booking->status === $key)>{{ $label }}</option>
                                    @endforeach
                                </select>
                                <button type="submit" class="rounded-lg bg-brand-700 px-2.5 py-1 text-xs font-semibold text-white hover:bg-brand-800 transition-colors">
                                    เปลี่ยน
                                </button>
                            </form>

                            <!-- Reschedule Modal / Details -->
                            <div>
                                <details class="inline-block text-left">
                                    <summary class="cursor-pointer text-xs font-semibold text-brand-700 hover:underline">
                                        เลื่อนวัน/เวลา →
                                    </summary>
                                    <div class="mt-2 p-3 rounded-xl bg-slate-50 border border-slate-200 text-left min-w-[240px]">
                                        <form method="post" action="{{ route('admin.bookings.reschedule', $booking) }}" class="space-y-2">
                                            @csrf
                                            @method('PATCH')
                                            <div>
                                                <label class="block text-[11px] font-semibold text-slate-600 mb-0.5">วันนัดใหม่</label>
                                                <input type="date" name="booking_date" min="{{ now()->toDateString() }}" value="{{ $booking->booking_date?->toDateString() }}" required class="w-full rounded border-slate-300 text-xs">
                                            </div>
                                            <div>
                                                <label class="block text-[11px] font-semibold text-slate-600 mb-0.5">รอบเวลาใหม่</label>
                                                <select name="time_slot_id" required class="w-full rounded border-slate-300 text-xs">
                                                    @foreach($slots as $slot)
                                                        <option value="{{ $slot->id }}" @selected($booking->time_slot_id == $slot->id)>
                                                            {{ substr($slot->start_time, 0, 5) }} - {{ substr($slot->end_time, 0, 5) }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div>
                                                <label class="block text-[11px] font-semibold text-slate-600 mb-0.5">หมอนวด</label>
                                                <select name="therapist_id" class="w-full rounded border-slate-300 text-xs">
                                                    <option value="">คงเดิม / ระบบจัดให้</option>
                                                    @foreach($therapists as $therapist)
                                                        <option value="{{ $therapist->id }}" @selected($booking->therapist_id == $therapist->id)>
                                                            {{ $therapist->displayName }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <button type="submit" class="w-full rounded-lg bg-brand-700 py-1.5 text-xs font-semibold text-white hover:bg-brand-800 transition-colors">
                                                บันทึกการเลื่อนนัด
                                            </button>
                                        </form>
                                    </div>
                                </details>
                                <span class="mx-1 text-slate-300">|</span>
                                <a href="{{ route('admin.bookings.history', $booking) }}" class="text-xs font-semibold text-slate-600 hover:text-brand-700 hover:underline">
                                    ประวัติ
                                </a>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="p-10 text-center text-slate-400 text-sm">
                            ไม่พบรายการจองคิวตามเงื่อนไขที่เลือก
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($bookings->hasPages())
        <div class="border-t border-slate-100 px-5 py-4">
            {{ $bookings->links() }}
        </div>
    @endif
</section>
@endsection

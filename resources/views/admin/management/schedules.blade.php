@extends('admin.layout')
@section('title', 'ตารางเวลาและวันหยุดคลินิก')

@section('content')
<div class="mb-6 flex flex-col justify-between gap-4 sm:flex-row sm:items-center">
    <div>
        <p class="text-xs font-semibold uppercase tracking-wider text-brand-600">Schedule & Holidays</p>
        <h1 class="text-2xl font-bold text-slate-900 mt-0.5">วันเปิดทำการ รอบเวลา และวันหยุด</h1>
        <p class="mt-1 text-sm text-slate-500">กำหนดเวลาเปิด-ปิดคลินิกประจำสัปดาห์ รอบเวลาให้บริการ และวันหยุดนักขัตฤกษ์</p>
    </div>
</div>

<!-- 1. Weekly Clinic Schedule -->
<section class="mb-6 rounded-2xl bg-white p-5 sm:p-6 shadow-sm border border-slate-100">
    <div class="pb-4 border-b border-slate-100 mb-5">
        <h2 class="font-bold text-slate-900 text-base">ตารางเปิด-ปิดคลินิกประจำสัปดาห์</h2>
        <p class="text-xs text-slate-500 mt-0.5">กำหนดวัน เวลาเปิด-ปิด และจำนวนผู้รับบริการสูงสุดต่อรอบ</p>
    </div>

    <form method="post" action="{{ route('admin.schedules.clinic.update') }}" class="space-y-5">
        @csrf
        @method('PUT')
        <div class="overflow-x-auto">
            <table class="w-full min-w-[700px] text-left text-sm">
                <thead class="bg-slate-50 text-slate-600">
                    <tr>
                        <th class="px-4 py-3">วัน</th>
                        <th class="px-4 py-3 text-center">สถานะ</th>
                        <th class="px-4 py-3">เวลาเปิด</th>
                        <th class="px-4 py-3">เวลาปิด</th>
                        <th class="px-4 py-3">สูงสุด/รอบ (คน)</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach(\App\Models\ClinicSchedule::DAYS_TH as $day => $label)
                        @php($row = $schedules->firstWhere('day_of_week', $day))
                        <tr class="hover:bg-slate-50/70 transition-colors">
                            <td class="px-4 py-3 font-semibold text-slate-800">
                                {{ $label }}
                            </td>
                            <td class="px-4 py-3 text-center">
                                <label class="inline-flex items-center gap-1.5 cursor-pointer">
                                    <input type="checkbox" name="open[{{ $day }}]" value="1" @checked($row?->is_open) class="h-4 w-4 rounded border-slate-300 text-brand-600 focus:ring-brand-500">
                                    <span class="text-xs font-medium text-slate-700">เปิด</span>
                                </label>
                            </td>
                            <td class="px-4 py-3">
                                <input type="time" name="schedules[{{ $day }}][open_time]" value="{{ substr($row?->open_time ?? '08:00', 0, 5) }}" class="rounded-xl border-slate-300 text-xs py-1.5 focus:border-brand-500 focus:ring-brand-200">
                            </td>
                            <td class="px-4 py-3">
                                <input type="time" name="schedules[{{ $day }}][close_time]" value="{{ substr($row?->close_time ?? '16:30', 0, 5) }}" class="rounded-xl border-slate-300 text-xs py-1.5 focus:border-brand-500 focus:ring-brand-200">
                            </td>
                            <td class="px-4 py-3">
                                <input type="number" min="1" max="100" name="schedules[{{ $day }}][max_bookings_per_slot]" value="{{ $row?->max_bookings_per_slot ?? 5 }}" class="w-24 rounded-xl border-slate-300 text-xs py-1.5 focus:border-brand-500 focus:ring-brand-200">
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="flex flex-wrap items-center justify-between gap-4 pt-3 border-t border-slate-100">
            <label class="flex items-center gap-2.5 text-sm font-semibold text-slate-800 cursor-pointer">
                <input name="booking_enabled" type="checkbox" value="1" @checked($bookingEnabled) class="h-4 w-4 rounded border-slate-300 text-brand-600 focus:ring-brand-500">
                <span>เปิดระบบรับจองคิวออนไลน์ (หากปิด ผู้รับบริการจะไม่สามารถกดจองได้)</span>
            </label>
            <button type="submit" class="rounded-xl bg-brand-700 px-6 py-2.5 text-sm font-semibold text-white shadow-md hover:bg-brand-800 transition-colors">
                บันทึกตารางคลินิก
            </button>
        </div>
    </form>
</section>

<!-- 2. Time Slots Manager -->
<section class="mb-6 rounded-2xl bg-white p-5 sm:p-6 shadow-sm border border-slate-100">
    <div class="pb-4 border-b border-slate-100 mb-5 flex items-center justify-between">
        <div>
            <h2 class="font-bold text-slate-900 text-base">รอบเวลาจอง (Time Slots)</h2>
            <p class="text-xs text-slate-500 mt-0.5">ระบบเริ่มต้น 9 รอบ สามารถเพิ่มหรือปรับเวลาตามรอบตรวจจริง (สูงสุด 30 รอบ)</p>
        </div>
    </div>

    <form method="post" action="{{ route('admin.schedules.slots.update') }}" class="space-y-4">
        @csrf
        @method('PUT')
        <div id="time-slots" class="space-y-2.5">
            @foreach($slots->where('is_active', true) as $i => $slot)
                <div class="slot-row grid grid-cols-[80px_130px_130px_1fr_60px] items-center gap-3 p-2.5 rounded-xl border border-slate-200 bg-slate-50/50">
                    <span class="slot-label text-xs font-bold text-brand-700">รอบที่ {{ $loop->iteration }}</span>
                    <div>
                        <label class="block text-[10px] text-slate-400 mb-0.5">เริ่ม</label>
                        <input type="time" name="slots[{{ $loop->index }}][start_time]" value="{{ substr($slot->start_time, 0, 5) }}" required class="w-full rounded-lg border-slate-300 text-xs py-1.5">
                    </div>
                    <div>
                        <label class="block text-[10px] text-slate-400 mb-0.5">สิ้นสุด</label>
                        <input type="time" name="slots[{{ $loop->index }}][end_time]" value="{{ substr($slot->end_time, 0, 5) }}" required class="w-full rounded-lg border-slate-300 text-xs py-1.5">
                    </div>
                    <div>
                        <label class="block text-[10px] text-slate-400 mb-0.5">ชื่อรอบ</label>
                        <input name="slots[{{ $loop->index }}][label_th]" value="{{ $slot->label_th }}" required placeholder="เช่น รอบเช้า 1" class="w-full rounded-lg border-slate-300 text-xs py-1.5">
                    </div>
                    <input type="hidden" name="slots[{{ $loop->index }}][is_active]" value="1">
                    <div class="text-right pt-3">
                        <button type="button" class="remove-slot rounded-lg p-1.5 text-rose-600 hover:bg-rose-50 transition-colors" title="ลบรอบเวลานี้">
                            ✕
                        </button>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="flex flex-wrap items-center justify-between gap-3 pt-3 border-t border-slate-100">
            <button id="add-slot" type="button" class="inline-flex items-center gap-1.5 rounded-xl border border-brand-300 bg-brand-50 px-4 py-2 text-xs font-semibold text-brand-800 hover:bg-brand-100 transition-colors">
                ＋ เพิ่มรอบเวลา
            </button>
            <button type="submit" class="rounded-xl bg-brand-700 px-6 py-2.5 text-sm font-semibold text-white shadow-md hover:bg-brand-800 transition-colors">
                บันทึกรอบเวลาทั้งหมด
            </button>
        </div>
    </form>
</section>

<!-- 3. Add Holiday -->
<section class="mb-6 rounded-2xl bg-white p-5 sm:p-6 shadow-sm border border-slate-100">
    <div class="pb-4 border-b border-slate-100 mb-5">
        <h2 class="font-bold text-slate-900 text-base">เพิ่มวันหยุดหรือปิดบริการเฉพาะวัน</h2>
        <p class="text-xs text-slate-500 mt-0.5">กำหนดวันหยุดนักขัตฤกษ์ หรือวันปิดทำการชั่วคราว</p>
    </div>

    <form method="post" action="{{ route('admin.schedules.holidays.store') }}" class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4 items-end">
        @csrf
        <div>
            <label class="mb-1 block text-xs font-semibold text-slate-700">วันที่ <span class="text-rose-500">*</span></label>
            <input type="date" name="date" required class="w-full rounded-xl border-slate-300 text-sm focus:border-brand-500 focus:ring-brand-200">
        </div>
        <div>
            <label class="mb-1 block text-xs font-semibold text-slate-700">ชื่อวันหยุด / เหตุผล <span class="text-rose-500">*</span></label>
            <input name="name" required placeholder="เช่น วันขึ้นปีใหม่" class="w-full rounded-xl border-slate-300 text-sm focus:border-brand-500 focus:ring-brand-200">
        </div>
        <div>
            <label class="flex items-center gap-2 text-sm font-medium text-slate-800 pb-2.5 cursor-pointer">
                <input type="checkbox" name="is_full_day" value="1" checked class="h-4 w-4 rounded border-slate-300 text-brand-600 focus:ring-brand-500">
                <span>ปิดทำการทั้งวัน</span>
            </label>
        </div>
        <div>
            <button type="submit" class="w-full rounded-xl bg-brand-700 py-2.5 text-sm font-semibold text-white shadow hover:bg-brand-800 transition-colors">
                เพิ่มวันหยุด
            </button>
        </div>
    </form>
</section>

<!-- 4. Holidays List Table -->
<section class="rounded-2xl bg-white shadow-sm border border-slate-100 overflow-hidden">
    <div class="px-5 py-4 border-b border-slate-100">
        <h2 class="font-bold text-slate-900 text-base">รายการวันหยุดที่กำหนดไว้</h2>
        <p class="text-xs text-slate-500 mt-0.5">รายการวันหยุดในระบบ (คิวจะไม่สามารถจองในวันที่ปิดได้)</p>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full min-w-[700px] text-left text-sm">
            <thead class="bg-slate-50 text-slate-600">
                <tr>
                    <th class="px-5 py-3">วันที่</th>
                    <th>ชื่อวันหยุด / รายละเอียด</th>
                    <th>ช่วงเวลาที่ปิด</th>
                    <th>หมายเหตุ</th>
                    <th class="px-5 text-right">การจัดการ</th>
                </tr>
            </thead>
            <tbody>
                @forelse($holidays as $holiday)
                    <tr class="border-t border-slate-100 hover:bg-slate-50/70 transition-colors">
                        <td class="px-5 py-3.5 font-semibold text-slate-800 whitespace-nowrap">
                            {{ $holiday->date->format('d/m/Y') }}
                            <span class="block text-[11px] font-normal text-slate-400">
                                {{ \Carbon\Carbon::parse($holiday->date)->locale('th')->isoFormat('dddd') }}
                            </span>
                        </td>
                        <td class="py-3.5 font-medium text-slate-900">
                            {{ $holiday->name }}
                        </td>
                        <td class="py-3.5">
                            <span class="inline-block rounded-full px-2.5 py-0.5 text-xs font-semibold {{ $holiday->is_full_day ? 'bg-rose-50 text-rose-700 border border-rose-200' : 'bg-amber-50 text-amber-700 border border-amber-200' }}">
                                {{ $holiday->is_full_day ? 'ปิดทั้งวัน' : substr($holiday->start_time, 0, 5).' – '.substr($holiday->end_time, 0, 5) }}
                            </span>
                        </td>
                        <td class="py-3.5 text-xs text-slate-500">
                            {{ $holiday->note ?: '—' }}
                        </td>
                        <td class="px-5 py-3.5 text-right">
                            <form method="post" action="{{ route('admin.schedules.holidays.delete', $holiday) }}" onsubmit="return confirm('ยืนยันลบวันหยุดนี้หรือไม่?')" class="inline-block">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-xs font-semibold text-rose-600 hover:text-rose-800 hover:underline">
                                    ลบ
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="p-8 text-center text-slate-400 text-sm">
                            ยังไม่มีวันหยุดที่กำหนดในระบบ
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($holidays->hasPages())
        <div class="border-t border-slate-100 px-5 py-4">
            {{ $holidays->links() }}
        </div>
    @endif
</section>

<script>
(() => {
    const box = document.getElementById('time-slots');
    if (!box) return;
    const renumber = () => {
        [...box.children].forEach((row, i) => {
            row.querySelector('.slot-label').textContent = `รอบที่ ${i + 1}`;
            row.querySelectorAll('input').forEach(input => {
                input.name = input.name.replace(/slots\[\d+\]/, `slots[${i}]`);
            });
        });
    };
    box.addEventListener('click', e => {
        if (e.target.matches('.remove-slot') && box.children.length > 1) {
            e.target.closest('.slot-row').remove();
            renumber();
        }
    });
    document.getElementById('add-slot')?.addEventListener('click', () => {
        if (box.children.length >= 30) return;
        const i = box.children.length;
        const row = document.createElement('div');
        row.className = 'slot-row grid grid-cols-[80px_130px_130px_1fr_60px] items-center gap-3 p-2.5 rounded-xl border border-slate-200 bg-slate-50/50';
        row.innerHTML = `
            <span class="slot-label text-xs font-bold text-brand-700">รอบที่ ${i + 1}</span>
            <div>
                <label class="block text-[10px] text-slate-400 mb-0.5">เริ่ม</label>
                <input type="time" name="slots[${i}][start_time]" required class="w-full rounded-lg border-slate-300 text-xs py-1.5">
            </div>
            <div>
                <label class="block text-[10px] text-slate-400 mb-0.5">สิ้นสุด</label>
                <input type="time" name="slots[${i}][end_time]" required class="w-full rounded-lg border-slate-300 text-xs py-1.5">
            </div>
            <div>
                <label class="block text-[10px] text-slate-400 mb-0.5">ชื่อรอบ</label>
                <input name="slots[${i}][label_th]" placeholder="เช่น รอบพิเศษ" required class="w-full rounded-lg border-slate-300 text-xs py-1.5">
            </div>
            <input type="hidden" name="slots[${i}][is_active]" value="1">
            <div class="text-right pt-3">
                <button type="button" class="remove-slot rounded-lg p-1.5 text-rose-600 hover:bg-rose-50 transition-colors" title="ลบรอบเวลานี้">✕</button>
            </div>
        `;
        box.append(row);
        renumber();
    });
})();
</script>
@endsection

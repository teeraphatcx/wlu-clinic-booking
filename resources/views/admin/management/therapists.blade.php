@extends('admin.layout')
@section('title', 'จัดการหมอนวดและแพทย์แผนไทย')

@section('content')
<div class="mb-6 flex flex-col justify-between gap-4 sm:flex-row sm:items-center">
    <div>
        <p class="text-xs font-semibold uppercase tracking-wider text-brand-600">Therapist Management</p>
        <h1 class="text-2xl font-bold text-slate-900 mt-0.5">จัดการแพทย์แผนไทย / หมอนวด</h1>
        <p class="mt-1 text-sm text-slate-500">ข้อมูลผู้ให้บริการ ตารางเวรประจำสัปดาห์ วันหยุด และช่วงเวลาปิดรับจอง</p>
    </div>
</div>

<!-- Add Therapist Accordion -->
<details class="group mb-6 rounded-2xl bg-white border border-slate-200 shadow-sm overflow-hidden" {{ $errors->any() ? 'open' : '' }}>
    <summary class="flex cursor-pointer items-center justify-between p-5 font-semibold text-slate-800 hover:bg-slate-50 transition-colors select-none">
        <div class="flex items-center gap-3">
            <span class="grid h-8 w-8 place-items-center rounded-xl bg-brand-50 text-brand-700 font-bold text-lg">＋</span>
            <div>
                <p class="font-bold text-slate-900 text-base">เพิ่มแพทย์แผนไทย / หมอนวดใหม่</p>
                <p class="text-xs text-slate-500 font-normal">บันทึกประวัติ กำหนดหัตถการที่เชี่ยวชาญ และเริ่มจัดตารางทำงาน</p>
            </div>
        </div>
        <span class="text-xs text-brand-700 font-semibold group-open:rotate-180 transition-transform duration-200">▼</span>
    </summary>

    <div class="border-t border-slate-100 p-5 sm:p-6 bg-slate-50/50">
        <form method="post" action="{{ route('admin.therapists.store') }}" enctype="multipart/form-data" class="space-y-4">
            @csrf
            <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                <div>
                    <label class="mb-1 block text-xs font-semibold text-slate-700">รหัสแพทย์/หมอนวด <span class="text-rose-500">*</span></label>
                    <input name="code" value="{{ old('code') }}" required placeholder="เช่น TH-001" class="w-full rounded-xl border-slate-300 text-sm focus:border-brand-500 focus:ring-brand-200">
                </div>
                <div>
                    <label class="mb-1 block text-xs font-semibold text-slate-700">ชื่อจริง <span class="text-rose-500">*</span></label>
                    <input name="first_name" value="{{ old('first_name') }}" required placeholder="ชื่อ" class="w-full rounded-xl border-slate-300 text-sm focus:border-brand-500 focus:ring-brand-200">
                </div>
                <div>
                    <label class="mb-1 block text-xs font-semibold text-slate-700">นามสกุล <span class="text-rose-500">*</span></label>
                    <input name="last_name" value="{{ old('last_name') }}" required placeholder="นามสกุล" class="w-full rounded-xl border-slate-300 text-sm focus:border-brand-500 focus:ring-brand-200">
                </div>
                <div>
                    <label class="mb-1 block text-xs font-semibold text-slate-700">ชื่อเล่น</label>
                    <input name="nickname" value="{{ old('nickname') }}" placeholder="ชื่อเล่น" class="w-full rounded-xl border-slate-300 text-sm focus:border-brand-500 focus:ring-brand-200">
                </div>
            </div>

            <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                <div>
                    <label class="mb-1 block text-xs font-semibold text-slate-700">เบอร์โทรศัพท์</label>
                    <input name="phone" value="{{ old('phone') }}" placeholder="เช่น 0812345678" class="w-full rounded-xl border-slate-300 text-sm focus:border-brand-500 focus:ring-brand-200">
                </div>
                <div>
                    <label class="mb-1 block text-xs font-semibold text-slate-700">เพศ <span class="text-rose-500">*</span></label>
                    <select name="gender" class="w-full rounded-xl border-slate-300 text-sm focus:border-brand-500 focus:ring-brand-200">
                        <option value="female" @selected(old('gender') === 'female')>หญิง</option>
                        <option value="male" @selected(old('gender') === 'male')>ชาย</option>
                        <option value="other" @selected(old('gender') === 'other')>ไม่ระบุ</option>
                    </select>
                </div>
                <div>
                    <label class="mb-1 block text-xs font-semibold text-slate-700">วันที่เริ่มปฏิบัติงาน</label>
                    <input name="start_date" type="date" value="{{ old('start_date') }}" class="w-full rounded-xl border-slate-300 text-sm focus:border-brand-500 focus:ring-brand-200">
                </div>
                <div class="flex items-center pt-6">
                    <label class="flex items-center gap-2.5 text-sm font-medium text-slate-800 cursor-pointer">
                        <input name="is_active" type="checkbox" value="1" checked class="h-4 w-4 rounded border-slate-300 text-brand-600 focus:ring-brand-500">
                        <span>เปิดใช้งานในระบบ</span>
                    </label>
                </div>
            </div>

            <div class="grid gap-4 sm:grid-cols-2">
                <div>
                    <label class="mb-1 block text-xs font-semibold text-slate-700">ประวัติการศึกษา / ความเชี่ยวชาญ</label>
                    <textarea name="bio" rows="3" placeholder="ระบุความเชี่ยวชาญ ใบประกอบวิชาชีพ หรือประวัติ..." class="w-full rounded-xl border-slate-300 text-sm focus:border-brand-500 focus:ring-brand-200">{{ old('bio') }}</textarea>
                </div>
                <div>
                    <label class="mb-1 block text-xs font-semibold text-slate-700">รูปแพทย์ / หมอนวด</label>
                    <input name="photo" type="file" accept="image/png,image/jpeg,image/webp" class="w-full rounded-xl border border-slate-300 bg-white p-2 text-xs file:mr-3 file:rounded-md file:border-0 file:bg-brand-50 file:px-3 file:py-1 file:font-medium file:text-brand-700">
                    <p class="mt-1 text-[11px] text-slate-400">รองรับ PNG, JPG หรือ WEBP ขนาดไม่เกิน 4 MB</p>
                </div>
                <div>
                    <label class="mb-1 block text-xs font-semibold text-slate-700">หัตถการที่สามารถให้บริการได้</label>
                    <select name="service_ids[]" multiple class="min-h-24 w-full rounded-xl border-slate-300 text-sm focus:border-brand-500 focus:ring-brand-200 p-2">
                        @foreach($services as $service)
                            <option value="{{ $service->id }}">
                                {{ $service->name_th }} ({{ $service->code }})
                            </option>
                        @endforeach
                    </select>
                    <p class="mt-1 text-[11px] text-slate-400">กด Ctrl (หรือ Cmd) ค้างไว้เพื่อเลือกหลายรายการ</p>
                </div>
            </div>

            <div class="pt-2 flex justify-end">
                <button type="submit" class="rounded-xl bg-brand-700 px-6 py-2.5 text-sm font-semibold text-white shadow-md hover:bg-brand-800 transition-colors">
                    บันทึกข้อมูลหมอนวด
                </button>
            </div>
        </form>
    </div>
</details>

<!-- Therapists Cards List -->
<div class="space-y-6">
    @forelse($therapists as $therapist)
        <section class="rounded-2xl bg-white border border-slate-200 shadow-sm p-5 sm:p-6">
            <!-- Header & Profile Info -->
            <div class="flex flex-wrap items-start justify-between gap-4 pb-4 border-b border-slate-100">
                <div class="flex items-center gap-4">
                    @if($therapist->photo)
                        <img src="{{ asset('storage/'.$therapist->photo) }}" alt="{{ $therapist->displayName }}" class="h-12 w-12 shrink-0 rounded-2xl object-cover border border-slate-200">
                    @else
                        <span class="grid h-12 w-12 shrink-0 place-items-center rounded-2xl bg-gradient-to-br from-brand-600 to-brand-800 text-white font-bold text-lg">
                            {{ mb_substr($therapist->first_name, 0, 1) }}
                        </span>
                    @endif
                    <div>
                        <div class="flex items-center gap-2">
                            <h2 class="text-lg font-bold text-slate-900">{{ $therapist->displayName }}</h2>
                            <span class="font-mono text-xs bg-slate-100 text-slate-600 px-2 py-0.5 rounded">
                                {{ $therapist->code }}
                            </span>
                        </div>
                        <div class="mt-1 flex flex-wrap items-center gap-2.5 text-xs text-slate-500">
                            @if($therapist->phone)
                                <a href="tel:{{ $therapist->phone }}" class="text-brand-700 hover:underline">
                                    📞 {{ $therapist->phone }}
                                </a>
                                <span>•</span>
                            @endif
                            <span>เพศ: {{ ['female' => 'หญิง', 'male' => 'ชาย', 'other' => 'ไม่ระบุ'][$therapist->gender] ?? $therapist->gender }}</span>
                            @if($therapist->start_date)
                                <span>•</span>
                                <span>เริ่มงาน: {{ $therapist->start_date->format('d/m/Y') }}</span>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="flex items-center gap-2">
                    <span class="rounded-full px-3 py-1 text-xs font-semibold {{ $therapist->is_active ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-slate-100 text-slate-500 border border-slate-200' }}">
                        {{ $therapist->is_active ? 'เปิดปฏิบัติงาน' : 'ระงับชั่วคราว' }}
                    </span>
                </div>
            </div>

            <!-- Edit Details Toggle -->
            <details class="group mt-4">
                <summary class="cursor-pointer text-xs font-semibold text-brand-700 hover:underline select-none">
                    แก้ไขข้อมูลทั่วไปของหมอนวด ▼
                </summary>
                <div class="mt-3 p-4 rounded-xl bg-slate-50 border border-slate-200">
                    <form method="post" action="{{ route('admin.therapists.update', $therapist) }}" enctype="multipart/form-data" class="space-y-3">
                        @csrf
                        @method('PUT')
                        <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-4">
                            <div>
                                <label class="block text-xs font-semibold text-slate-600 mb-0.5">รหัส</label>
                                <input name="code" value="{{ old('code', $therapist->code) }}" required class="w-full rounded border-slate-300 text-xs">
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-slate-600 mb-0.5">ชื่อ</label>
                                <input name="first_name" value="{{ old('first_name', $therapist->first_name) }}" required class="w-full rounded border-slate-300 text-xs">
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-slate-600 mb-0.5">นามสกุล</label>
                                <input name="last_name" value="{{ old('last_name', $therapist->last_name) }}" required class="w-full rounded border-slate-300 text-xs">
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-slate-600 mb-0.5">ชื่อเล่น</label>
                                <input name="nickname" value="{{ old('nickname', $therapist->nickname) }}" class="w-full rounded border-slate-300 text-xs">
                            </div>
                        </div>

                        <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-4">
                            <div>
                                <label class="block text-xs font-semibold text-slate-600 mb-0.5">เบอร์โทรศัพท์</label>
                                <input name="phone" value="{{ old('phone', $therapist->phone) }}" class="w-full rounded border-slate-300 text-xs">
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-slate-600 mb-0.5">เพศ</label>
                                <select name="gender" class="w-full rounded border-slate-300 text-xs">
                                    <option value="female" @selected($therapist->gender === 'female')>หญิง</option>
                                    <option value="male" @selected($therapist->gender === 'male')>ชาย</option>
                                    <option value="other" @selected($therapist->gender === 'other')>ไม่ระบุ</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-slate-600 mb-0.5">วันที่เริ่มงาน</label>
                                <input name="start_date" type="date" value="{{ $therapist->start_date?->format('Y-m-d') }}" class="w-full rounded border-slate-300 text-xs">
                            </div>
                            <div class="flex items-center pt-4">
                                <label class="flex items-center gap-2 text-xs font-medium cursor-pointer">
                                    <input name="is_active" type="checkbox" value="1" @checked($therapist->is_active)>
                                    <span>เปิดใช้งาน</span>
                                </label>
                            </div>
                        </div>

                        <div class="grid gap-3 sm:grid-cols-2">
                            <div>
                                <label class="block text-xs font-semibold text-slate-600 mb-0.5">ประวัติย่อ</label>
                                <textarea name="bio" rows="2" class="w-full rounded border-slate-300 text-xs">{{ old('bio', $therapist->bio) }}</textarea>
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-slate-600 mb-0.5">เปลี่ยนรูปแพทย์ / หมอนวด</label>
                                @if($therapist->photo)
                                    <img src="{{ asset('storage/'.$therapist->photo) }}" alt="{{ $therapist->displayName }}" class="mb-2 h-16 w-16 rounded-xl object-cover border border-slate-200">
                                @endif
                                <input name="photo" type="file" accept="image/png,image/jpeg,image/webp" class="w-full rounded border border-slate-300 bg-white p-1.5 text-[11px] file:mr-2 file:rounded file:border-0 file:bg-brand-50 file:px-2 file:py-1 file:font-medium file:text-brand-700">
                                <p class="mt-1 text-[10px] text-slate-400">เว้นว่างไว้หากไม่ต้องการเปลี่ยนรูป</p>
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-slate-600 mb-0.5">หัตถการที่ให้บริการ</label>
                                <select name="service_ids[]" multiple class="min-h-20 w-full rounded border-slate-300 text-xs p-1">
                                    @foreach($services as $service)
                                        <option value="{{ $service->id }}" @selected($therapist->services->contains('id', $service->id))>
                                            {{ $service->name_th }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <button type="submit" class="rounded-lg bg-brand-700 px-4 py-1.5 text-xs font-semibold text-white hover:bg-brand-800">
                            บันทึกข้อมูล
                        </button>
                    </form>
                </div>
            </details>

            <!-- Three Sub-Sections: Weekly Schedule, Days Off, Blocked Periods -->
            <div class="mt-5 grid gap-5 border-t border-slate-100 pt-5 lg:grid-cols-3">
                <!-- 1. Weekly Schedule -->
                <div class="rounded-xl bg-slate-50 p-4 border border-slate-100">
                    <h3 class="text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">
                        📅 เวรประจำสัปดาห์
                    </h3>
                    <form method="post" action="{{ route('admin.therapists.schedules.store', $therapist) }}" class="space-y-2 mb-3">
                        @csrf
                        <select name="day_of_week" required class="w-full rounded border-slate-300 text-xs font-medium">
                            @foreach(\App\Models\ClinicSchedule::DAYS_TH as $day => $name)
                                <option value="{{ $day }}">{{ $name }}</option>
                            @endforeach
                        </select>
                        <div class="grid grid-cols-2 gap-1.5">
                            <div>
                                <label class="block text-[10px] text-slate-500">เวลาเริ่ม</label>
                                <input name="start_time" type="time" value="08:00" required class="w-full rounded border-slate-300 text-xs">
                            </div>
                            <div>
                                <label class="block text-[10px] text-slate-500">เวลาสิ้นสุด</label>
                                <input name="end_time" type="time" value="16:30" required class="w-full rounded border-slate-300 text-xs">
                            </div>
                        </div>
                        <div class="grid grid-cols-2 gap-1.5">
                            <div>
                                <label class="block text-[10px] text-slate-500">พักเริ่ม</label>
                                <input name="break_start" type="time" class="w-full rounded border-slate-300 text-xs">
                            </div>
                            <div>
                                <label class="block text-[10px] text-slate-500">พักสิ้นสุด</label>
                                <input name="break_end" type="time" class="w-full rounded border-slate-300 text-xs">
                            </div>
                        </div>
                        <div class="flex items-center justify-between pt-1">
                            <label class="flex items-center gap-1.5 text-xs cursor-pointer">
                                <input name="is_working" type="checkbox" value="1" checked>
                                <span>ปฏิบัติงาน</span>
                            </label>
                            <button type="submit" class="rounded bg-brand-700 px-3 py-1 text-xs font-semibold text-white hover:bg-brand-800">
                                บันทึกเวร
                            </button>
                        </div>
                    </form>

                    <!-- Schedule List -->
                    <div class="space-y-1 text-xs border-t border-slate-200 pt-2">
                        @forelse($therapist->schedules as $sch)
                            <div class="flex justify-between py-0.5 {{ $sch->is_working ? 'text-slate-800' : 'text-slate-400' }}">
                                <span class="font-medium">{{ \App\Models\ClinicSchedule::DAYS_TH[$sch->day_of_week] }}</span>
                                <span>
                                    @if($sch->is_working)
                                        {{ substr($sch->start_time, 0, 5) }} - {{ substr($sch->end_time, 0, 5) }}
                                        @if($sch->break_start)
                                            <span class="text-[10px] text-slate-500">(พัก {{ substr($sch->break_start, 0, 5) }}-{{ substr($sch->break_end, 0, 5) }})</span>
                                        @endif
                                    @else
                                        (วันหยุด)
                                    @endif
                                </span>
                            </div>
                        @empty
                            <p class="text-slate-400 text-center py-2">ยังไม่มีตารางประจำสัปดาห์</p>
                        @endforelse
                    </div>
                </div>

                <!-- 2. Days Off -->
                <div class="rounded-xl bg-slate-50 p-4 border border-slate-100">
                    <h3 class="text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">
                        🏖 วันหยุดรายบุคคล
                    </h3>
                    <form method="post" action="{{ route('admin.therapists.days-off.store', $therapist) }}" class="space-y-2 mb-3">
                        @csrf
                        <input name="date" type="date" required class="w-full rounded border-slate-300 text-xs">
                        <input name="reason" placeholder="เหตุผล (เช่น ลาพักผ่อน)" class="w-full rounded border-slate-300 text-xs">
                        <button type="submit" class="w-full rounded bg-brand-700 py-1 text-xs font-semibold text-white hover:bg-brand-800">
                            เพิ่มวันหยุด
                        </button>
                    </form>

                    <!-- Days Off List -->
                    <div class="space-y-1 text-xs border-t border-slate-200 pt-2">
                        @forelse($therapist->daysOff as $off)
                            <div class="flex justify-between py-0.5">
                                <span class="font-medium text-slate-800">{{ $off->date->format('d/m/Y') }}</span>
                                <span class="text-slate-500">{{ $off->reason ?: 'วันหยุด' }}</span>
                            </div>
                        @empty
                            <p class="text-slate-400 text-center py-2">ไม่มีวันหยุดรายบุคคล</p>
                        @endforelse
                    </div>
                </div>

                <!-- 3. Blocked Periods -->
                <div class="rounded-xl bg-slate-50 p-4 border border-slate-100">
                    <h3 class="text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">
                        ⛔ ปิดรับจองเฉพาะช่วงเวลา
                    </h3>
                    <form method="post" action="{{ route('admin.therapists.blocks.store', $therapist) }}" class="space-y-2 mb-3">
                        @csrf
                        <div>
                            <label class="block text-[10px] text-slate-500">ตั้งแต่</label>
                            <input name="start_datetime" type="datetime-local" required class="w-full rounded border-slate-300 text-xs">
                        </div>
                        <div>
                            <label class="block text-[10px] text-slate-500">ถึง</label>
                            <input name="end_datetime" type="datetime-local" required class="w-full rounded border-slate-300 text-xs">
                        </div>
                        <input name="reason" placeholder="เหตุผล (เช่น ประชุมวิชาการ)" class="w-full rounded border-slate-300 text-xs">
                        <button type="submit" class="w-full rounded bg-brand-700 py-1 text-xs font-semibold text-white hover:bg-brand-800">
                            เพิ่มช่วงปิดรับจอง
                        </button>
                    </form>

                    <!-- Blocked Periods List -->
                    <div class="space-y-1 text-xs border-t border-slate-200 pt-2">
                        @forelse($therapist->blockedPeriods as $block)
                            <div class="py-1 border-b border-slate-100 last:border-0">
                                <p class="font-medium text-slate-800">
                                    {{ $block->start_datetime->format('d/m H:i') }} - {{ $block->end_datetime->format('d/m H:i') }}
                                </p>
                                @if($block->reason)
                                    <p class="text-[11px] text-slate-500">{{ $block->reason }}</p>
                                @endif
                            </div>
                        @empty
                            <p class="text-slate-400 text-center py-2">ไม่มีช่วงเวลาที่ถูกปิดรับจอง</p>
                        @endforelse
                    </div>
                </div>
            </div>
        </section>
    @empty
        <div class="rounded-2xl bg-white p-12 text-center text-slate-400 border border-slate-200 shadow-sm">
            ยังไม่มีข้อมูลแพทย์แผนไทย/หมอนวดในระบบ
        </div>
    @endforelse
</div>

@if($therapists->hasPages())
    <div class="mt-6">
        {{ $therapists->links() }}
    </div>
@endif
@endsection

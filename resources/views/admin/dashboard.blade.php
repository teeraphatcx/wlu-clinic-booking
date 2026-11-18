@extends('admin.layout')
@section('title', 'แดชบอร์ดภาพรวม')

@section('content')
<section class="relative mb-7 overflow-hidden rounded-[28px] border border-brand-800/30 bg-gradient-to-br from-brand-900 via-brand-800 to-brand-700 p-6 text-white shadow-xl shadow-brand-900/10 sm:p-8">
    <div class="pointer-events-none absolute -right-24 -top-28 h-80 w-80 rounded-full border border-white/10"></div>
    <div class="pointer-events-none absolute -bottom-36 right-24 h-72 w-72 rounded-full border border-white/10"></div>

    <div class="relative z-10 flex flex-col justify-between gap-8 xl:flex-row xl:items-end">
        <div class="max-w-2xl">
            <div class="mb-4 inline-flex items-center gap-2 rounded-full border border-white/15 bg-white/10 px-3 py-1.5 text-[11px] font-semibold uppercase tracking-[0.18em] text-brand-100">
                <span class="relative flex h-2 w-2">
                    <span class="absolute inline-flex h-full w-full animate-ping rounded-full bg-emerald-300 opacity-70"></span>
                    <span class="relative inline-flex h-2 w-2 rounded-full bg-emerald-300"></span>
                </span>
                Live overview
            </div>
            <p class="text-sm font-medium text-brand-200">สวัสดี, {{ auth('admin')->user()?->name ?? 'เจ้าหน้าที่' }}</p>
            <h1 class="mt-2 max-w-xl font-heading text-3xl font-bold leading-tight tracking-tight text-white sm:text-4xl">ภาพรวมคลินิกของคุณ<br class="hidden sm:block"> พร้อมดูแลทุกคิวสำคัญ</h1>
            <p class="mt-3 max-w-xl text-sm leading-6 text-brand-100/80">ดูสถานะการจอง ภาระงาน และบริการที่กำลังเป็นที่นิยมได้จากมุมมองเดียว</p>

            <div class="mt-6 flex flex-wrap items-center gap-2.5 text-xs font-medium">
                <span class="inline-flex items-center gap-2 rounded-xl border border-white/10 bg-white/10 px-3 py-2 text-brand-50">
                    <svg class="h-4 w-4 text-brand-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2h14a2 2 0 002-2V7a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"/></svg>
                    {{ now()->locale('th')->isoFormat('D MMMM YYYY') }}
                </span>
                <span class="inline-flex items-center gap-2 rounded-xl border border-white/10 bg-white/10 px-3 py-2 text-brand-50">
                    <span class="h-2 w-2 rounded-full bg-emerald-300"></span>
                    ระบบรับจองเปิดให้บริการ
                </span>
            </div>
        </div>

        <div class="w-full max-w-sm rounded-3xl border border-white/15 bg-white/10 p-5 backdrop-blur-sm">
            <div class="flex items-start justify-between gap-4">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-[0.16em] text-brand-200">ภาพรวมวันนี้</p>
                    <p class="mt-1 text-sm text-brand-50/80">คิวที่ต้องดูแลในวันนี้</p>
                </div>
                <span class="grid h-11 w-11 place-items-center rounded-2xl bg-white/15 text-brand-50">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2h14a2 2 0 002-2V7a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"/></svg>
                </span>
            </div>
            <div class="mt-6 grid grid-cols-3 divide-x divide-white/10">
                <div class="pr-3">
                    <p class="text-[11px] text-brand-200">ทั้งหมด</p>
                    <p class="mt-1 text-2xl font-bold text-white">{{ number_format($stats['today']) }}</p>
                </div>
                <div class="px-3">
                    <p class="text-[11px] text-brand-200">ยืนยันแล้ว</p>
                    <p class="mt-1 text-2xl font-bold text-white">{{ number_format($stats['confirmed']) }}</p>
                </div>
                <div class="pl-3">
                    <p class="text-[11px] text-brand-200">รอยืนยัน</p>
                    <p class="mt-1 text-2xl font-bold text-white">{{ number_format($stats['pending']) }}</p>
                </div>
            </div>
        </div>
    </div>

    <div class="relative z-10 mt-7 flex flex-wrap items-center gap-2.5">
        <a href="{{ route('admin.bookings.index') }}" class="inline-flex items-center gap-2 rounded-xl bg-white px-4 py-2.5 text-sm font-semibold text-brand-800 shadow-lg shadow-brand-900/10 transition hover:-translate-y-0.5 hover:bg-brand-50">
            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            <span>จัดการคิว</span>
        </a>
        <a href="{{ route('admin.reports.index') }}" class="inline-flex items-center gap-2 rounded-xl border border-white/15 bg-white/10 px-4 py-2.5 text-sm font-semibold text-white transition hover:-translate-y-0.5 hover:bg-white/15">
            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414A2 2 0 0119 9.414V19a2 2 0 01-2 2z"/></svg>
            <span>ดูรายงานทั้งหมด</span>
        </a>
    </div>
</section>

<div class="mb-3 flex flex-wrap items-end justify-between gap-3">
    <div>
        <p class="text-sm font-bold text-slate-900">สถานะการจองวันนี้</p>
        <p class="mt-0.5 text-xs text-slate-500">ตัวเลขสำคัญที่ช่วยให้ทีมวางแผนงานได้เร็วขึ้น</p>
    </div>
    <span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-50 px-3 py-1.5 text-[11px] font-semibold text-emerald-700">
        <span class="h-1.5 w-1.5 rounded-full bg-emerald-500"></span>
        อัปเดตล่าสุดแบบเรียลไทม์
    </span>
</div>

<div class="hidden mb-6 flex flex-wrap items-center justify-between gap-4">
    <div>
        <p class="text-xs font-semibold uppercase tracking-wider text-brand-600">Overview Dashboard</p>
        <h1 class="text-2xl font-bold text-slate-900 mt-0.5">แดชบอร์ดภาพรวม</h1>
        <p class="mt-1 text-sm text-slate-500">สรุปข้อมูลการจองคิว สถิติบริการ และภาระงานประจำวัน</p>
    </div>
    <div class="flex flex-wrap items-center gap-2.5">
        <a href="{{ route('admin.bookings.index') }}" class="inline-flex items-center gap-2 rounded-xl border border-brand-200 bg-white px-4 py-2.5 text-sm font-semibold text-brand-700 shadow-sm hover:bg-brand-50 transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            <span>จัดการคิว</span>
        </a>
        <a href="{{ route('admin.reports.index') }}" class="inline-flex items-center gap-2 rounded-xl bg-brand-700 px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-brand-800 transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
            <span>ดูรายงานทั้งหมด</span>
        </a>
    </div>
</div>

<!-- Primary Status Cards -->
<div class="grid grid-cols-2 gap-4 sm:grid-cols-2 lg:grid-cols-4 xl:grid-cols-5">
    <!-- Today -->
    <div class="rounded-2xl border border-brand-200 bg-gradient-to-br from-brand-700 to-brand-900 p-5 text-white shadow-sm transition duration-200 hover:-translate-y-1 hover:shadow-xl col-span-2 sm:col-span-1 xl:col-span-1">
        <div class="flex items-center justify-between">
            <span class="text-xs font-medium text-brand-200">คิววันนี้</span>
            <span class="inline-flex h-2 w-2 rounded-full bg-emerald-400 animate-pulse"></span>
        </div>
        <p class="mt-3 text-3xl font-extrabold tracking-tight">{{ number_format($stats['today']) }}</p>
        <p class="mt-1 text-xs text-brand-200">นัดหมายที่ยังไม่ถูกยกเลิก</p>
    </div>

    <!-- Confirmed -->
    <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm transition duration-200 hover:-translate-y-1 hover:shadow-lg">
        <div class="flex items-center justify-between">
            <span class="text-xs font-semibold text-brand-700">ยืนยันแล้ว</span>
            <span class="rounded-lg bg-brand-50 p-1.5 text-brand-600">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </span>
        </div>
        <p class="mt-3 text-2xl font-bold text-slate-800">{{ number_format($stats['confirmed']) }}</p>
        <p class="mt-1 text-xs text-slate-500">พร้อมเข้ารับบริการ</p>
    </div>

    <!-- Completed -->
    <div class="rounded-2xl border border-emerald-100 bg-emerald-50/60 p-5 shadow-sm transition duration-200 hover:-translate-y-1 hover:shadow-lg">
        <div class="flex items-center justify-between">
            <span class="text-xs font-semibold text-emerald-800">เข้ารับบริการแล้ว</span>
            <span class="rounded-lg bg-emerald-100 p-1.5 text-emerald-700">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
            </span>
        </div>
        <p class="mt-3 text-2xl font-bold text-emerald-900">{{ number_format($stats['completed']) }}</p>
        <p class="mt-1 text-xs text-emerald-700">เสร็จสิ้นเรียบร้อย</p>
    </div>

    <!-- Pending -->
    <div class="rounded-2xl border border-amber-100 bg-amber-50/60 p-5 shadow-sm transition duration-200 hover:-translate-y-1 hover:shadow-lg">
        <div class="flex items-center justify-between">
            <span class="text-xs font-semibold text-amber-800">รอยืนยัน</span>
            <span class="rounded-lg bg-amber-100 p-1.5 text-amber-700">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </span>
        </div>
        <p class="mt-3 text-2xl font-bold text-amber-900">{{ number_format($stats['pending']) }}</p>
        <p class="mt-1 text-xs text-amber-700">รอเจ้าหน้าที่ตรวจสอบ</p>
    </div>

    <!-- Cancelled & No Show -->
    <div class="rounded-2xl border border-rose-100 bg-rose-50/60 p-5 shadow-sm transition duration-200 hover:-translate-y-1 hover:shadow-lg">
        <div class="flex items-center justify-between">
            <span class="text-xs font-semibold text-rose-800">ยกเลิก / ไม่มาตามนัด</span>
            <span class="rounded-lg bg-rose-100 p-1.5 text-rose-700">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </span>
        </div>
        <p class="mt-3 text-2xl font-bold text-rose-900">
            {{ number_format($stats['cancelled']) }}
            <span class="text-sm font-normal text-rose-600">({{ number_format($stats['no_show']) }} ไม่มา)</span>
        </p>
        <p class="mt-1 text-xs text-rose-700">คิวที่ไม่สำเร็จ</p>
    </div>
</div>

<!-- Secondary Metrics -->
<div class="mt-4 grid grid-cols-2 gap-3 sm:grid-cols-4">
    <div class="rounded-xl border border-slate-200 bg-white p-3.5 shadow-sm flex items-center justify-between">
        <div>
            <p class="text-xs text-slate-500">คิวเดือนนี้</p>
            <p class="text-lg font-bold text-slate-800 mt-0.5">{{ number_format($stats['month']) }}</p>
        </div>
        <span class="text-xs text-brand-600 font-semibold bg-brand-50 px-2 py-1 rounded-md">เดือนปัจจุบัน</span>
    </div>
    <div class="rounded-xl border border-slate-200 bg-white p-3.5 shadow-sm flex items-center justify-between">
        <div>
            <p class="text-xs text-slate-500">คิวปีนี้</p>
            <p class="text-lg font-bold text-slate-800 mt-0.5">{{ number_format($stats['year']) }}</p>
        </div>
        <span class="text-xs text-brand-600 font-semibold bg-brand-50 px-2 py-1 rounded-md">ปี {{ now()->year + 543 }}</span>
    </div>
    <div class="rounded-xl border border-slate-200 bg-white p-3.5 shadow-sm flex items-center justify-between">
        <div>
            <p class="text-xs text-slate-500">การจองทั้งหมด</p>
            <p class="text-lg font-bold text-slate-800 mt-0.5">{{ number_format($stats['all']) }}</p>
        </div>
        <span class="text-xs text-slate-500 bg-slate-100 px-2 py-1 rounded-md">สะสม</span>
    </div>
    <div class="rounded-xl border border-slate-200 bg-white p-3.5 shadow-sm flex items-center justify-between">
        <div>
            <p class="text-xs text-slate-500">หัตถการเปิดบริการ</p>
            <p class="text-lg font-bold text-slate-800 mt-0.5">{{ number_format($stats['services']) }}</p>
        </div>
        <span class="text-xs text-emerald-700 font-semibold bg-emerald-50 px-2 py-1 rounded-md">เปิดใช้งาน</span>
    </div>
</div>

<!-- Booking Trend Chart -->
<section class="mt-6 rounded-2xl bg-white p-5 sm:p-6 shadow-sm border border-slate-100">
    <div class="flex flex-wrap items-center justify-between gap-3 pb-4 border-b border-slate-100">
        <div>
            <h2 class="font-bold text-slate-900 text-base">สถิติแนวโน้มการจอง</h2>
            <p class="text-xs text-slate-500 mt-0.5">จำนวนการจองคิวตามช่วงเวลา</p>
        </div>
        <form method="get" action="{{ route('admin.dashboard') }}">
            <select name="period" onchange="this.form.submit()" class="rounded-xl border-slate-300 text-xs font-medium text-slate-700 focus:border-brand-500 focus:ring-brand-200">
                <option value="daily" @selected($period === 'daily')>รายวัน (14 วันล่าสุด)</option>
                <option value="monthly" @selected($period === 'monthly')>รายเดือน (12 เดือนล่าสุด)</option>
                <option value="yearly" @selected($period === 'yearly')>รายปี (5 ปีล่าสุด)</option>
            </select>
        </form>
    </div>

    @php
        $maxChart = max(1, (int) $chart->max('count'));
    @endphp
    <div class="mt-6">
        <div class="flex h-56 items-end gap-2 overflow-x-auto border-b border-slate-200 pb-2 px-2">
            @foreach($chart as $point)
                <div class="group flex min-w-8 flex-1 flex-col items-center justify-end gap-1.5" title="{{ $point['label'] }}: {{ $point['count'] }} คิว">
                    <span class="text-[11px] font-semibold text-slate-600 transition-opacity {{ $point['count'] > 0 ? 'opacity-100' : 'opacity-0 group-hover:opacity-100' }}">
                        {{ $point['count'] }}
                    </span>
                    <div class="w-full max-w-10 rounded-t-lg bg-gradient-to-t from-brand-700 to-brand-500 transition-all duration-200 group-hover:from-brand-600 group-hover:to-brand-400 group-hover:shadow-md"
                         style="height: {{ max(4, (int) round(($point['count'] / $maxChart) * 170)) }}px">
                    </div>
                    <span class="mt-1 whitespace-nowrap text-[10px] font-medium text-slate-500 group-hover:text-brand-700">
                        {{ $point['label'] }}
                    </span>
                </div>
            @endforeach
        </div>
    </div>
</section>

<!-- Two-Column Section: Popular Services & Therapist Load -->
<div class="mt-6 grid gap-6 lg:grid-cols-2">
    <!-- Popular Services -->
    <section class="rounded-2xl bg-white p-5 sm:p-6 shadow-sm border border-slate-100 flex flex-col">
        <div class="flex items-center justify-between pb-3 border-b border-slate-100 mb-4">
            <div>
                <h2 class="font-bold text-slate-900 text-base">หัตถการยอดนิยม (30 วันล่าสุด)</h2>
                <p class="text-xs text-slate-500">บริการที่มีการจองมากที่สุด</p>
            </div>
            <a href="{{ route('admin.services.index') }}" class="text-xs font-semibold text-brand-700 hover:underline">จัดการหัตถการ →</a>
        </div>
        <div class="space-y-4 flex-1">
            @php
                $maxServiceCount = max(1, (int) $popularServices->max('bookings_count'));
            @endphp
            @forelse($popularServices as $service)
                <div>
                    <div class="flex items-center justify-between text-sm mb-1">
                        <span class="font-medium text-slate-800">{{ $service->name_th }}</span>
                        <span class="font-bold text-brand-800 text-xs">{{ number_format($service->bookings_count) }} คิว</span>
                    </div>
                    <div class="w-full bg-slate-100 rounded-full h-2 overflow-hidden">
                        <div class="bg-brand-600 h-2 rounded-full transition-all duration-500" style="width: {{ round(($service->bookings_count / $maxServiceCount) * 100) }}%"></div>
                    </div>
                </div>
            @empty
                <p class="text-sm text-slate-400 py-6 text-center">ยังไม่มีข้อมูลการจองในรอบ 30 วัน</p>
            @endforelse
        </div>
    </section>

    <!-- Therapist Workload -->
    <section class="rounded-2xl bg-white p-5 sm:p-6 shadow-sm border border-slate-100 flex flex-col">
        <div class="flex items-center justify-between pb-3 border-b border-slate-100 mb-4">
            <div>
                <h2 class="font-bold text-slate-900 text-base">คิววันนี้แยกตามหมอนวด</h2>
                <p class="text-xs text-slate-500">ภาระงานประจำวันของแพทย์แผนไทย</p>
            </div>
            <a href="{{ route('admin.therapists.index') }}" class="text-xs font-semibold text-brand-700 hover:underline">จัดการหมอนวด →</a>
        </div>
        <div class="space-y-4 flex-1">
            @php
                $maxTherapistCount = max(1, (int) $therapistStats->max('today_bookings_count'));
            @endphp
            @forelse($therapistStats as $therapist)
                <div>
                    <div class="flex items-center justify-between text-sm mb-1">
                        <span class="font-medium text-slate-800">{{ $therapist->displayName }}</span>
                        <span class="font-bold text-brand-800 text-xs">{{ $therapist->today_bookings_count }} คิว</span>
                    </div>
                    <div class="w-full bg-slate-100 rounded-full h-2 overflow-hidden">
                        <div class="bg-clinic-600 h-2 rounded-full transition-all duration-500" style="width: {{ $therapist->today_bookings_count > 0 ? round(($therapist->today_bookings_count / $maxTherapistCount) * 100) : 0 }}%"></div>
                    </div>
                </div>
            @empty
                <p class="text-sm text-slate-400 py-6 text-center">ยังไม่มีข้อมูลหมอนวดในระบบ</p>
            @endforelse
        </div>
    </section>
</div>

<!-- Today's Queue List -->
<section class="mt-6 rounded-2xl bg-white shadow-sm border border-slate-100 overflow-hidden">
    <div class="flex flex-wrap items-center justify-between gap-3 px-5 py-4 border-b border-slate-100">
        <div>
            <h2 class="font-bold text-slate-900 text-base">รายการคิววันนี้</h2>
            <p class="text-xs text-slate-500 mt-0.5">คิวทั้งหมดที่นัดหมายในวันที่ {{ now()->locale('th')->isoFormat('D MMMM YYYY') }}</p>
        </div>
        <a href="{{ route('admin.bookings.index', ['date' => now()->toDateString()]) }}" class="rounded-xl border border-brand-200 bg-brand-50 px-3.5 py-1.5 text-xs font-semibold text-brand-700 hover:bg-brand-100 transition-colors">
            ดูในหน้าจัดการคิว →
        </a>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full min-w-[750px] text-left text-sm">
            <thead class="bg-slate-50 text-slate-600">
                <tr>
                    <th class="px-5 py-3">รอบเวลา</th>
                    <th>รหัสการจอง</th>
                    <th>ผู้รับบริการ</th>
                    <th>หัตถการ</th>
                    <th>หมอนวด</th>
                    <th>สถานะ</th>
                    <th class="px-5 text-right">รายละเอียด</th>
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
                @forelse($todayBookings as $booking)
                    <tr class="border-t border-slate-100 hover:bg-slate-50/70 transition-colors">
                        <td class="px-5 py-3.5 whitespace-nowrap">
                            <span class="inline-flex items-center font-semibold text-slate-800 bg-slate-100 px-2.5 py-1 rounded-lg text-xs">
                                ⏱ {{ substr($booking->timeSlot?->start_time ?? '', 0, 5) }} - {{ substr($booking->timeSlot?->end_time ?? '', 0, 5) }}
                            </span>
                        </td>
                        <td class="whitespace-nowrap">
                            <a class="font-mono font-bold text-brand-700 hover:text-brand-900 hover:underline" href="{{ route('admin.bookings.history', $booking) }}">
                                {{ $booking->booking_no }}
                            </a>
                        </td>
                        <td>
                            <p class="font-medium text-slate-900">{{ $booking->customer?->full_name }}</p>
                            @if($booking->customer?->phone)
                                <a href="tel:{{ $booking->customer->phone }}" class="text-xs text-slate-500 hover:text-brand-600">
                                    {{ $booking->customer->phone }}
                                </a>
                            @endif
                        </td>
                        <td>
                            <p class="text-slate-800 font-medium">{{ $booking->service?->name_th }}</p>
                            <span class="text-xs text-slate-500">{{ $booking->service?->duration_minutes }} นาที · ฿{{ number_format($booking->service?->price, 0) }}</span>
                        </td>
                        <td>
                            <span class="text-xs font-medium text-slate-700">
                                {{ $booking->therapist?->displayName ?? 'ระบบจัดให้อัตโนมัติ' }}
                            </span>
                        </td>
                        <td>
                            <span class="inline-block rounded-full border px-2.5 py-0.5 text-xs font-semibold {{ $statusBadgeMap[$booking->status] ?? 'bg-slate-100 text-slate-700 border-slate-200' }}">
                                {{ \App\Models\Booking::STATUSES[$booking->status] ?? $booking->status }}
                            </span>
                        </td>
                        <td class="px-5 text-right whitespace-nowrap">
                            <a href="{{ route('admin.bookings.history', $booking) }}" class="inline-flex items-center text-xs font-semibold text-brand-700 hover:text-brand-900 hover:underline">
                                ประวัติ →
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="py-10 text-center text-slate-400 text-sm">
                            ไม่มีคิวสำหรับวันนี้
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</section>
@endsection

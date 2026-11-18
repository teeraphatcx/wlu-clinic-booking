@extends('layouts.app')

@section('title', 'หน้าแรก')

@section('content')
@if($clinicSettings['notice'])
<div class="bg-brand-50 px-4 py-3 text-center text-sm font-medium text-brand-800">{{ $clinicSettings['notice'] }}</div>
@endif
<!-- Hero Section -->
<div class="relative bg-gradient-to-b from-brand-900 via-clinic-900 to-slate-900 text-white overflow-hidden py-16 lg:py-24">
    <!-- Subtle Background Pattern -->
    <div class="absolute inset-0 opacity-10 bg-[radial-gradient(#14b8a6_1px,transparent_1px)] [background-size:16px_16px]"></div>

    <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 items-center">
            
            <!-- Left Hero Content -->
            <div class="lg:col-span-7 space-y-6 text-center lg:text-left">
                
                <!-- Today Status Badge -->
                <div class="inline-flex items-center gap-2.5 px-4 py-2 rounded-full text-xs sm:text-sm font-medium {{ $isOpenToday ? 'bg-emerald-500/20 text-emerald-300 border border-emerald-500/30' : 'bg-rose-500/20 text-rose-300 border border-rose-500/30' }}">
                    <span class="w-2.5 h-2.5 rounded-full {{ $isOpenToday ? 'bg-emerald-400 animate-ping' : 'bg-rose-400' }}"></span>
                    @if($holidayToday)
                        <span>ปิดทำการเนื่องใน: {{ $holidayToday->name }}</span>
                    @elseif($isOpenToday)
                        <span>วันนี้เปิดทำการ: {{ substr($todaySchedule->open_time, 0, 5) }} - {{ substr($todaySchedule->close_time, 0, 5) }} น.</span>
                    @else
                        <span>วันนี้คลินิกปิดให้บริการ (เปิด จันทร์ - ศุกร์)</span>
                    @endif
                </div>

                <h1 class="font-heading font-extrabold text-3xl sm:text-4xl lg:text-5xl tracking-tight leading-tight">
                    ดูแลสุขภาพแบบองค์รวม <br>
                    <span class="text-transparent bg-clip-text bg-gradient-to-r from-brand-400 to-amber-300">
                        ด้วยศาสตร์แพทย์แผนไทยประยุกต์
                    </span>
                </h1>

                <p class="text-slate-300 text-base sm:text-lg max-w-2xl leading-relaxed mx-auto lg:mx-0">
                    {{ $clinicSettings['description'] ?: 'บริการนวดรักษาโรค นวดฟื้นฟูสุขภาพ ประคบสมุนไพรสด อบสมุนไพร และการดูแลมารดาหลังคลอด โดยแพทย์แผนไทยประยุกต์และผู้เชี่ยวชาญ พร้อมระบบนัดหมายล่วงหน้าที่สะดวก รวดเร็ว ไม่ต้องรอคิวนาน' }}
                </p>

                <!-- Action CTA Buttons -->
                <div class="pt-4 flex flex-col sm:flex-row items-center justify-center lg:justify-start gap-4">
                    <a href="{{ route('booking.index') }}" class="w-full sm:w-auto inline-flex items-center justify-center gap-3 px-8 py-4 rounded-xl font-heading font-bold text-base text-white bg-gradient-to-r from-brand-600 via-brand-500 to-amber-500 hover:from-brand-500 hover:to-amber-500 shadow-xl shadow-brand-500/30 hover:shadow-brand-500/50 hover:-translate-y-0.5 transition-all duration-200">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                        <span>จองคิวออนไลน์ทันที</span>
                    </a>
                    
                    <a href="{{ route('booking.check-form') }}" class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-6 py-4 rounded-xl font-heading font-semibold text-base text-slate-200 bg-white/10 hover:bg-white/20 border border-white/15 backdrop-blur-sm transition-all duration-200">
                        <svg class="w-5 h-5 text-brand-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                        <span>ตรวจสอบ / เลื่อนนัดหมาย</span>
                    </a>
                </div>

                <!-- Fast features list -->
                <div class="grid grid-cols-3 gap-4 pt-6 border-t border-slate-700/60 text-slate-300 text-xs sm:text-sm">
                    <div class="flex items-center gap-2 justify-center lg:justify-start">
                        <svg class="w-4 h-4 text-brand-400 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path></svg>
                        <span>แพทย์แผนไทยผู้เชี่ยวชาญ</span>
                    </div>
                    <div class="flex items-center gap-2 justify-center lg:justify-start">
                        <svg class="w-4 h-4 text-brand-400 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path></svg>
                        <span>มาตรฐานโรงพยาบาล</span>
                    </div>
                    <div class="flex items-center gap-2 justify-center lg:justify-start">
                        <svg class="w-4 h-4 text-brand-400 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path></svg>
                        <span>ระบบนัดหมายแม่นยำ</span>
                    </div>
                </div>

            </div>

            <!-- Right Hero Card / Quick Booking Helper -->
            <div class="lg:col-span-5">
                <div class="bg-white/95 backdrop-blur-md rounded-2xl p-6 sm:p-8 text-slate-900 shadow-2xl border border-white/20">
                    <div class="flex items-center justify-between pb-5 border-b border-slate-100">
                        <div>
                            <span class="text-xs font-bold text-brand-600 uppercase tracking-wider">นัดหมายรวดเร็ว</span>
                            <h2 class="font-heading font-bold text-xl text-slate-800">ขั้นตอนการจองคิวง่ายๆ</h2>
                        </div>
                        <div class="w-10 h-10 rounded-full bg-brand-50 text-brand-600 flex items-center justify-center font-bold">
                            3 สเต็ป
                        </div>
                    </div>

                    <div class="space-y-4 py-5 text-sm">
                        <div class="flex items-start gap-4">
                            <div class="w-8 h-8 rounded-full bg-brand-100 text-brand-700 font-bold flex items-center justify-center shrink-0">1</div>
                            <div>
                                <h3 class="font-semibold text-slate-900">เลือกหัตถการ</h3>
                                <p class="text-slate-500 text-xs mt-0.5">เลือกบริการที่ท่านต้องการ เช่น นวดรักษา อบสมุนไพร ทับหม้อเกลือ</p>
                            </div>
                        </div>

                        <div class="flex items-start gap-4">
                            <div class="w-8 h-8 rounded-full bg-brand-100 text-brand-700 font-bold flex items-center justify-center shrink-0">2</div>
                            <div>
                                <h3 class="font-semibold text-slate-900">เลือกวัน เวลา และแพทย์</h3>
                                <p class="text-slate-500 text-xs mt-0.5">เลือกวันว่างในปฏิทิน รอบเวลาที่สะดวก และแพทย์ประจำการ</p>
                            </div>
                        </div>

                        <div class="flex items-start gap-4">
                            <div class="w-8 h-8 rounded-full bg-brand-100 text-brand-700 font-bold flex items-center justify-center shrink-0">3</div>
                            <div>
                                <h3 class="font-semibold text-slate-900">รับรหัสยืนยันการจอง</h3>
                                <p class="text-slate-500 text-xs mt-0.5">กรอกข้อมูลผู้รับบริการ และรับรหัสใบนัดหมาย WLU ทันที</p>
                            </div>
                        </div>
                    </div>

                    <div class="pt-4 border-t border-slate-100">
                        <a href="{{ route('booking.index') }}" class="w-full py-3.5 px-4 rounded-xl bg-slate-900 hover:bg-brand-600 text-white font-heading font-semibold text-sm flex items-center justify-center gap-2 transition-colors">
                            <span>เริ่มทำรายการจองคิว</span>
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                        </a>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

<!-- Services Grid Section -->
<section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-end gap-4 mb-10">
        <div>
            <span class="text-xs font-bold text-brand-600 uppercase tracking-wider">บริการเด่น</span>
            <h2 class="font-heading font-bold text-2xl sm:text-3xl text-slate-900 mt-1">บริการหัตถการแพทย์แผนไทย</h2>
            <p class="text-slate-500 text-sm mt-1">ตรวจรักษาและดูแลสุขภาพด้วยศาสตร์การแพทย์ดั้งเดิมผสมผสานองค์ความรู้ปัจจุบัน</p>
        </div>
        <a href="{{ route('services.index') }}" class="inline-flex items-center gap-1.5 text-sm font-semibold text-brand-600 hover:text-brand-700 group">
            <span>ดูบริการทั้งหมด</span>
            <svg class="w-4 h-4 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
        </a>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 lg:gap-8">
        @foreach($services as $service)
            <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm hover:shadow-lg transition-all duration-300 flex flex-col overflow-hidden group hover:-translate-y-1">
                
                <!-- Service Card Header Decor -->
                <div class="h-3 bg-gradient-to-r from-brand-500 to-clinic-600"></div>

                <div class="p-6 flex-1 flex flex-col justify-between">
                    <div>
                        <div class="flex items-center justify-between gap-2 mb-3">
                            <span class="inline-flex items-center gap-1 text-xs font-medium px-2.5 py-1 rounded-md bg-clinic-50 text-clinic-700 border border-clinic-200">
                                <svg class="w-3.5 h-3.5 text-clinic-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                <span>{{ $service->duration_minutes }} นาที</span>
                            </span>
                            @if($service->requires_therapist_selection)
                                <span class="text-xs px-2 py-0.5 rounded bg-brand-50 text-brand-700 border border-brand-200">เลือกแพทย์ได้</span>
                            @else
                                <span class="text-xs px-2 py-0.5 rounded bg-slate-100 text-slate-600">จัดแพทย์อัตโนมัติ</span>
                            @endif
                        </div>

                        <h3 class="font-heading font-bold text-lg text-slate-900 group-hover:text-brand-600 transition-colors line-clamp-1">
                            {{ $service->name_th }}
                        </h3>
                        <p class="text-xs text-slate-400 mt-0.5 mb-3">{{ $service->name_en }}</p>

                        <p class="text-slate-600 text-xs sm:text-sm line-clamp-2 leading-relaxed">
                            {{ $service->description }}
                        </p>
                    </div>

                    <div class="pt-6 mt-4 border-t border-slate-100 flex items-center justify-between">
                        <div>
                            <span class="text-xs text-slate-400">อัตราค่าบริการ</span>
                            <div class="font-heading font-extrabold text-xl text-slate-900">
                                ฿{{ number_format($service->price, 0) }}
                            </div>
                        </div>
                        <a href="{{ route('booking.select-date', $service) }}" class="inline-flex items-center gap-1.5 px-4 py-2.5 rounded-xl font-heading font-semibold text-xs text-white bg-brand-600 hover:bg-brand-500 shadow-sm transition-colors">
                            <span>จองคิว</span>
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                        </a>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</section>

<!-- News & Announcements Section -->
@if($news->count() > 0)
<section class="bg-slate-100/70 border-y border-slate-200 py-16">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center max-w-2xl mx-auto mb-10">
            <span class="text-xs font-bold text-clinic-700 uppercase tracking-wider">ประชาสัมพันธ์</span>
            <h2 class="font-heading font-bold text-2xl sm:text-3xl text-slate-900 mt-1">ข่าวสารและประกาศจากคลินิก</h2>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            @foreach($news as $item)
                <div class="bg-white rounded-2xl p-6 border border-slate-200/80 shadow-sm hover:shadow-md transition-shadow">
                    <div class="flex items-center gap-2 mb-3">
                        <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold {{ $item->type === 'announcement' ? 'bg-amber-100 text-amber-800' : ($item->type === 'promotion' ? 'bg-emerald-100 text-emerald-800' : 'bg-blue-100 text-blue-800') }}">
                            {{ $item->type === 'announcement' ? 'ประกาศ' : ($item->type === 'promotion' ? 'โปรโมชั่น' : 'ข่าวสาร') }}
                        </span>
                        @if($item->published_at)
                            <span class="text-xs text-slate-400">{{ $item->published_at->format('d/m/Y') }}</span>
                        @endif
                    </div>
                    <h3 class="font-heading font-bold text-base text-slate-900 mb-2 line-clamp-2">
                        {{ $item->title }}
                    </h3>
                    <p class="text-slate-600 text-xs sm:text-sm line-clamp-3 leading-relaxed">
                        {{ $item->excerpt ?: strip_tags($item->content) }}
                    </p>
                </div>
            @endforeach
        </div>
    </div>
</section>
@endif

<!-- Clinic Location & Hours Info Section -->
<section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
    <div class="bg-gradient-to-r from-clinic-900 to-slate-900 rounded-3xl p-8 sm:p-12 text-white shadow-xl">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 items-center">
            <div class="space-y-4">
                <span class="text-xs font-bold text-brand-400 uppercase tracking-wider">การเดินทางและการรับบริการ</span>
                <h2 class="font-heading font-bold text-2xl sm:text-3xl text-white">
                    {{ $clinicSettings['hospital'] }}
                </h2>
                <p class="text-slate-300 text-sm leading-relaxed">
                    {{ $clinicSettings['address'] }}
                </p>
                <div class="pt-2 flex flex-wrap gap-4 text-sm text-slate-200">
                    <div class="flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-brand-400"></span>
                        <span>มีที่จอดรถสะดวกสบาย</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-brand-400"></span>
                        <span>รองรับสิทธิการเบิกจ่ายตามระเบียบ</span>
                    </div>
                </div>
            </div>

            <div class="bg-white/10 backdrop-blur-md rounded-2xl p-6 border border-white/10 space-y-3">
                <h3 class="font-heading font-bold text-lg text-white">ตารางเวลาเปิดให้บริการ</h3>
                <div class="space-y-2 text-xs sm:text-sm text-slate-200">
                    @foreach($clinicSchedules as $clinicSchedule)
                    <div class="flex justify-between gap-3 border-b border-white/10 py-1">
                        <span>{{ $clinicSchedule->dayNameTh }}</span>
                        @if($clinicSchedule->is_open)
                            <span class="font-semibold text-emerald-300">{{ substr($clinicSchedule->open_time,0,5) }} - {{ substr($clinicSchedule->close_time,0,5) }} น.</span>
                        @else
                            <span class="font-semibold text-rose-300">ปิดทำการ</span>
                        @endif
                    </div>
                    @endforeach
                    @if($clinicSettings['opening_hours_text'])<p class="pt-1 text-xs text-brand-100">{{ $clinicSettings['opening_hours_text'] }}</p>@endif
                    <div class="flex justify-between py-1"><span>วันหยุดพิเศษ</span><span class="font-semibold text-rose-300">ตามประกาศของคลินิก</span></div>
                </div>
                <div class="pt-2 text-center text-xs text-slate-400">
                    *กรุณามาถึงคลินิกก่อนเวลานัดหมาย 15 นาที เพื่อตรวจคัดกรองเบื้องต้น
                </div>
            </div>
        </div>
    </div>
</section>
@endsection

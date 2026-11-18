@extends('layouts.app')

@section('title', 'ติดต่อเรา')

@section('content')
<div class="max-w-3xl mx-auto px-4 py-8">
    <h1 class="text-2xl font-bold text-brand-800 mb-6">ติดต่อเรา</h1>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div class="bg-white border border-gray-200 rounded-xl p-5 shadow-sm">
            <h2 class="font-bold text-gray-900 mb-4">ข้อมูลคลินิก</h2>
            <div class="space-y-3 text-sm text-gray-700">
                <div class="flex gap-3">
                    <svg class="w-5 h-5 text-brand-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                    <p>{{ $clinicSettings['name_th'] }} {{ $clinicSettings['hospital'] }}<br>{{ $clinicSettings['address'] }}</p>
                </div>
                <div class="flex gap-3">
                    <svg class="w-5 h-5 text-brand-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                    </svg>
                    <p>{{ $clinicSettings['phone'] }}</p>
                </div>
                <div class="flex gap-3">
                    <svg class="w-5 h-5 text-brand-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <div>
                        <p class="font-medium">เวลาทำการ</p>
                        @foreach($clinicSettings['schedules'] as $schedule)
                            <p>{{ $schedule->dayNameTh }}: @if($schedule->is_open){{ substr($schedule->open_time,0,5) }} - {{ substr($schedule->close_time,0,5) }} น.@elseปิดทำการ@endif</p>
                        @endforeach
                        <p class="text-gray-500">วันหยุดพิเศษเป็นไปตามประกาศ</p>
                    </div>
                </div>
            </div>
        </div>
        @if($clinicSettings['email'] || $clinicSettings['map_url'])
            <div class="mt-5 rounded-xl border border-brand-100 bg-white p-5 text-sm text-slate-700">
                @if($clinicSettings['email'])<p>อีเมล: <a class="text-brand-700 underline" href="mailto:{{ $clinicSettings['email'] }}">{{ $clinicSettings['email'] }}</a></p>@endif
                @if($clinicSettings['map_url'])<p class="mt-2"><a class="text-brand-700 underline" href="{{ $clinicSettings['map_url'] }}" target="_blank" rel="noopener noreferrer">ดูแผนที่คลินิก</a></p>@endif
            </div>
        @endif
        <div class="bg-brand-50 border border-brand-100 rounded-xl p-5">
            <h2 class="font-bold text-brand-800 mb-4">จองคิวออนไลน์</h2>
            <p class="text-sm text-brand-700 mb-4">สะดวก รวดเร็ว ไม่ต้องรอคิวนาน จองล่วงหน้าได้ทาง Online ตลอด 24 ชั่วโมง</p>
            <a href="{{ route('booking.index') }}"
               class="block w-full bg-brand-600 hover:bg-brand-700 text-white text-center font-semibold py-3 rounded-xl transition-colors">
                จองคิวออนไลน์ →
            </a>
            <a href="{{ route('booking.check-form') }}"
               class="block w-full mt-2 bg-white hover:bg-gray-50 text-brand-700 text-center font-medium py-2.5 rounded-xl transition-colors border border-brand-200 text-sm">
                ตรวจสอบสถานะการจอง
            </a>
        </div>
    </div>
</div>
@endsection

@extends('layouts.app')

@section('title', 'จองคิวสำเร็จ - ' . $booking->booking_no)

@section('content')
<div class="max-w-lg mx-auto px-4 py-10">
    <!-- Success Card -->
    <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
        <!-- Header -->
        <div class="bg-green-500 p-6 text-center">
            <div class="w-16 h-16 bg-white rounded-full flex items-center justify-center mx-auto mb-3">
                <svg class="w-10 h-10 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                </svg>
            </div>
            <h1 class="text-xl font-bold text-white">จองคิวสำเร็จ!</h1>
            <p class="text-green-100 text-sm mt-1">กรุณาบันทึกรหัสการจองไว้</p>
        </div>

        <!-- Booking Number -->
        <div class="bg-brand-50 p-5 text-center border-b border-brand-100">
            <p class="text-sm text-brand-600 mb-1">รหัสการจองของคุณ</p>
            <div class="text-3xl font-bold text-brand-800 tracking-widest font-mono">
                {{ $booking->booking_no }}
            </div>
            <button onclick="navigator.clipboard.writeText('{{ $booking->booking_no }}').then(() => alert('คัดลอกรหัสการจองแล้ว'))"
                class="mt-2 text-xs text-brand-500 underline hover:text-brand-700">
                📋 คัดลอกรหัส
            </button>
        </div>

        <!-- Booking Details -->
        <div class="p-5 space-y-3">
            <div class="flex items-center gap-3 p-3 bg-gray-50 rounded-lg">
                <svg class="w-5 h-5 text-brand-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                </svg>
                <div>
                    <p class="text-xs text-gray-500">บริการ</p>
                    <p class="font-medium text-gray-800">{{ $booking->service->name_th }}</p>
                    <p class="text-sm text-gray-500">{{ $booking->service->duration_minutes }} นาที · {{ number_format($booking->service->price, 0) }} บาท</p>
                </div>
            </div>

            <div class="flex items-center gap-3 p-3 bg-gray-50 rounded-lg">
                <svg class="w-5 h-5 text-brand-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
                <div>
                    <p class="text-xs text-gray-500">วันและเวลานัดหมาย</p>
                    <p class="font-medium text-gray-800">
                        {{ \Carbon\Carbon::parse($booking->booking_date)->locale('th')->isoFormat('dddd ที่ D MMMM YYYY') }}
                    </p>
                    <p class="text-sm text-gray-500">
                        {{ $booking->timeSlot?->label_th ?? ($booking->timeSlot?->start_time . ' - ' . $booking->timeSlot?->end_time) }}
                    </p>
                </div>
            </div>

            @if($booking->therapist)
            <div class="flex items-center gap-3 p-3 bg-gray-50 rounded-lg">
                <svg class="w-5 h-5 text-brand-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                </svg>
                <div>
                    <p class="text-xs text-gray-500">หมอนวด</p>
                    <p class="font-medium text-gray-800">{{ $booking->therapist->full_name }}</p>
                </div>
            </div>
            @endif

            <div class="flex items-center gap-3 p-3 bg-gray-50 rounded-lg">
                <svg class="w-5 h-5 text-brand-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <div>
                    <p class="text-xs text-gray-500">สถานะ</p>
                    @php
                        $statusClasses = [
                            'pending' => 'bg-yellow-100 text-yellow-700',
                            'confirmed' => 'bg-blue-100 text-blue-700',
                            'completed' => 'bg-green-100 text-green-700',
                            'cancelled' => 'bg-red-100 text-red-700',
                            'no_show' => 'bg-gray-100 text-gray-600',
                        ];
                    @endphp
                    <span class="inline-block text-sm font-medium px-2 py-0.5 rounded-full {{ $statusClasses[$booking->status] ?? 'bg-yellow-100 text-yellow-700' }}">
                        {{ \App\Models\Booking::STATUSES[$booking->status] ?? $booking->status }}
                    </span>
                </div>
            </div>
        </div>

        <!-- Instructions -->
        <div class="mx-5 mb-5 p-4 bg-blue-50 border border-blue-100 rounded-xl text-sm text-blue-800 space-y-1">
            <p class="font-medium">📌 คำแนะนำ</p>
            <ul class="space-y-1 text-blue-700 mt-1">
                <li>• บันทึกรหัสการจองไว้ เพื่อใช้ตรวจสอบสถานะ</li>
                <li>• กรุณามาก่อนเวลานัด 15 นาที</li>
                <li>• หากต้องการยกเลิก กรุณาแจ้งล่วงหน้าอย่างน้อย {{ \App\Models\Setting::get('cancel_before_hours', 24) }} ชั่วโมง</li>
            </ul>
        </div>

        <!-- Action Buttons -->
        <div class="px-5 pb-5 grid grid-cols-2 gap-3">
            <a href="{{ route('booking.show', $booking->booking_no) }}"
               class="bg-brand-600 hover:bg-brand-700 text-white text-center font-semibold py-2.5 rounded-xl transition-colors text-sm">
                ตรวจสอบสถานะ
            </a>
            <a href="{{ route('booking.index') }}"
               class="bg-gray-100 hover:bg-gray-200 text-gray-700 text-center font-semibold py-2.5 rounded-xl transition-colors text-sm">
                จองคิวใหม่
            </a>
        </div>
    </div>
</div>
@endsection

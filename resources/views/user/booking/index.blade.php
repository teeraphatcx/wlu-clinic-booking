@extends('layouts.app')

@section('title', 'จองคิวออนไลน์ - เลือกบริการ')

@section('content')
<div class="max-w-4xl mx-auto px-4 py-8">
    <!-- Header -->
    <div class="text-center mb-8">
        <h1 class="text-2xl font-bold text-brand-800 mb-2">จองคิวออนไลน์</h1>
        <p class="text-gray-600">เลือกบริการที่ต้องการ</p>
    </div>

    <!-- Breadcrumb -->
    <nav class="flex mb-6 text-sm" aria-label="Breadcrumb">
        <ol class="flex items-center space-x-2">
            <li><a href="{{ route('home') }}" class="text-brand-600 hover:text-brand-700">หน้าหลัก</a></li>
            <li class="text-gray-400">/</li>
            <li class="text-gray-700 font-medium">เลือกบริการ</li>
        </ol>
    </nav>

    <!-- Step Indicator -->
    <div class="flex items-center justify-center mb-8">
        <div class="flex items-center">
            <div class="flex items-center justify-center w-8 h-8 rounded-full bg-brand-600 text-white font-bold text-sm">1</div>
            <span class="ml-2 text-sm font-medium text-brand-700">เลือกบริการ</span>
        </div>
        <div class="w-12 h-0.5 bg-gray-300 mx-3"></div>
        <div class="flex items-center">
            <div class="flex items-center justify-center w-8 h-8 rounded-full bg-gray-200 text-gray-500 font-bold text-sm">2</div>
            <span class="ml-2 text-sm text-gray-500">เลือกวัน-เวลา</span>
        </div>
        <div class="w-12 h-0.5 bg-gray-300 mx-3"></div>
        <div class="flex items-center">
            <div class="flex items-center justify-center w-8 h-8 rounded-full bg-gray-200 text-gray-500 font-bold text-sm">3</div>
            <span class="ml-2 text-sm text-gray-500">ยืนยันการจอง</span>
        </div>
    </div>

    <!-- Services Grid -->
    @if($services->isEmpty())
        <div class="text-center py-12 bg-gray-50 rounded-xl">
            <svg class="w-16 h-16 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <p class="text-gray-500">ไม่มีบริการที่เปิดให้จองในขณะนี้</p>
        </div>
    @else
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            @foreach($services as $service)
                <a href="{{ route('booking.select-date', $service) }}"
                   class="group bg-white border border-gray-200 rounded-xl p-5 hover:border-brand-400 hover:shadow-md transition-all duration-200">
                    <div class="flex items-start gap-4">
                        @if($service->image)
                            <img src="{{ Storage::url($service->image) }}" alt="{{ $service->name_th }}" width="80" height="80" loading="lazy" decoding="async"
                                 class="w-20 h-20 rounded-lg object-cover flex-shrink-0">
                        @else
                            <div class="w-20 h-20 rounded-lg bg-brand-50 flex items-center justify-center flex-shrink-0">
                                <svg class="w-10 h-10 text-brand-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                                </svg>
                            </div>
                        @endif
                        <div class="flex-1 min-w-0">
                            <h3 class="font-semibold text-gray-900 group-hover:text-brand-700 transition-colors leading-snug">
                                {{ $service->name_th }}
                            </h3>
                            @if($service->description)
                                <p class="text-sm text-gray-500 mt-1 line-clamp-2">{{ $service->description }}</p>
                            @endif
                            <div class="flex items-center gap-3 mt-2 flex-wrap">
                                <span class="inline-flex items-center gap-1 text-sm text-brand-700 font-medium">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    {{ $service->duration_minutes }} นาที
                                </span>
                                <span class="inline-flex items-center gap-1 text-sm text-green-700 font-medium">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    {{ number_format($service->price, 0) }} บาท
                                </span>
                            </div>
                            @unless($service->requires_therapist_selection)
                                <span class="inline-block mt-2 text-xs bg-blue-50 text-blue-600 px-2 py-0.5 rounded-full">
                                    ระบบจัดหมอให้อัตโนมัติ
                                </span>
                            @endunless
                        </div>
                        <svg class="w-5 h-5 text-gray-400 group-hover:text-brand-500 flex-shrink-0 mt-1 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                        </svg>
                    </div>
                </a>
            @endforeach
        </div>
    @endif
</div>
@endsection

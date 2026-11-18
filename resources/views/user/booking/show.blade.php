@extends('layouts.app')

@section('title', 'รายละเอียดการจอง ' . $booking->booking_no)

@section('content')
<div class="max-w-2xl mx-auto px-4 py-6">
    <!-- Back -->
    <a href="{{ route('booking.check-form') }}" class="inline-flex items-center gap-1 text-sm text-brand-600 hover:text-brand-700 mb-5">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
        </svg>
        ค้นหาการจองอื่น
    </a>

    <!-- Success/Error Messages -->
    @if(session('success'))
        <div class="mb-5 bg-green-50 border border-green-200 rounded-xl p-4 text-green-800 text-sm">
            ✅ {{ session('success') }}
        </div>
    @endif
    @if($errors->any())
        <div class="mb-5 bg-red-50 border border-red-200 rounded-xl p-4">
            @foreach($errors->all() as $error)
                <p class="text-red-700 text-sm">⚠️ {{ $error }}</p>
            @endforeach
        </div>
    @endif

    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
        <!-- Header with Status -->
        <div class="p-5 border-b border-gray-100 flex items-center justify-between flex-wrap gap-3">
            <div>
                <p class="text-xs text-gray-500 mb-0.5">รหัสการจอง</p>
                <p class="text-xl font-bold text-brand-800 font-mono">{{ $booking->booking_no }}</p>
            </div>
            @php
                $statusColors = [
                    'pending' => 'bg-yellow-100 text-yellow-700',
                    'confirmed' => 'bg-blue-100 text-blue-700',
                    'completed' => 'bg-green-100 text-green-700',
                    'cancelled' => 'bg-red-100 text-red-700',
                    'no_show' => 'bg-gray-100 text-gray-600',
                ];
                $statusLabels = \App\Models\Booking::STATUSES;
            @endphp
            <span class="px-3 py-1.5 rounded-full font-medium text-sm {{ $statusColors[$booking->status] ?? 'bg-gray-100 text-gray-600' }}">
                {{ $statusLabels[$booking->status] ?? $booking->status }}
            </span>
        </div>

        <!-- Booking Details -->
        <div class="p-5 space-y-4">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="bg-gray-50 rounded-lg p-3">
                    <p class="text-xs text-gray-500 mb-0.5">ชื่อผู้จอง</p>
                    <p class="font-medium text-gray-800">{{ $booking->customer->first_name }} {{ $booking->customer->last_name }}</p>
                </div>
                <div class="bg-gray-50 rounded-lg p-3">
                    <p class="text-xs text-gray-500 mb-0.5">เบอร์โทร</p>
                    <p class="font-medium text-gray-800">{{ $booking->customer->phone }}</p>
                </div>
                <div class="bg-gray-50 rounded-lg p-3">
                    <p class="text-xs text-gray-500 mb-0.5">บริการ</p>
                    <p class="font-medium text-gray-800">{{ $booking->service->name_th }}</p>
                    <p class="text-sm text-gray-500">{{ $booking->service->duration_minutes }} นาที · {{ number_format($booking->service->price, 0) }} บาท</p>
                </div>
                <div class="bg-gray-50 rounded-lg p-3">
                    <p class="text-xs text-gray-500 mb-0.5">วัน-เวลานัดหมาย</p>
                    <p class="font-medium text-gray-800">
                        {{ \Carbon\Carbon::parse($booking->booking_date)->locale('th')->isoFormat('D MMM YYYY') }}
                    </p>
                    <p class="text-sm text-gray-500">
                        {{ $booking->timeSlot?->label_th ?? ($booking->timeSlot?->start_time . ' - ' . $booking->timeSlot?->end_time) }}
                    </p>
                </div>
                @if($booking->therapist)
                <div class="bg-gray-50 rounded-lg p-3">
                    <p class="text-xs text-gray-500 mb-0.5">หมอนวด</p>
                    <p class="font-medium text-gray-800">{{ $booking->therapist->full_name }}</p>
                </div>
                @endif
                @if($booking->notes)
                <div class="bg-gray-50 rounded-lg p-3 sm:col-span-2">
                    <p class="text-xs text-gray-500 mb-0.5">หมายเหตุ</p>
                    <p class="text-sm text-gray-700">{{ $booking->notes }}</p>
                </div>
                @endif
            </div>

            @if($booking->status === 'cancelled')
                <div class="bg-red-50 border border-red-100 rounded-lg p-3 text-sm">
                    <p class="text-red-700 font-medium">สาเหตุการยกเลิก: {{ $booking->cancel_reason }}</p>
                    @if($booking->cancelled_at)
                        <p class="text-red-500 text-xs mt-0.5">ยกเลิกเมื่อ: {{ $booking->cancelled_at->format('d/m/Y H:i') }}</p>
                    @endif
                </div>
            @endif
        </div>

        <!-- Action Buttons -->
        @if(in_array($booking->status, ['pending', 'confirmed']))
        <div class="px-5 pb-5 flex flex-col sm:flex-row gap-3">
            @if($canReschedule === true)
                <a href="{{ route('booking.reschedule-form', $booking->booking_no) }}"
                   class="flex-1 bg-brand-600 hover:bg-brand-700 text-white text-center font-semibold py-2.5 rounded-xl transition-colors text-sm">
                    📅 เปลี่ยนวันนัด
                </a>
            @else
                <div class="flex-1 bg-gray-100 text-gray-400 text-center font-medium py-2.5 rounded-xl text-sm cursor-not-allowed"
                     title="{{ is_string($canReschedule) ? $canReschedule : '' }}">
                    📅 เปลี่ยนวันนัด (ไม่สามารถ)
                </div>
            @endif

            @if($canCancel === true)
                <button onclick="document.getElementById('cancelModal').classList.remove('hidden')"
                   class="flex-1 bg-red-50 hover:bg-red-100 text-red-600 text-center font-semibold py-2.5 rounded-xl transition-colors text-sm border border-red-200">
                    ❌ ยกเลิกการจอง
                </button>
            @else
                <div class="flex-1 bg-gray-50 text-gray-400 text-center font-medium py-2.5 rounded-xl text-sm cursor-not-allowed border border-gray-100"
                     title="{{ is_string($canCancel) ? $canCancel : '' }}">
                    ❌ ยกเลิก (ไม่สามารถ)
                </div>
            @endif
        </div>
        @endif

        <!-- Booking History -->
        @if($booking->histories->isNotEmpty())
        <div class="border-t border-gray-100 px-5 py-4">
            <h3 class="text-sm font-semibold text-gray-700 mb-3">ประวัติการเปลี่ยนแปลง</h3>
            <div class="space-y-2">
                @foreach($booking->histories as $history)
                    <div class="flex gap-3 text-xs">
                        <div class="flex-shrink-0 w-1.5 h-1.5 rounded-full bg-brand-400 mt-1.5"></div>
                        <div>
                            <p class="text-gray-700">{{ $history->notes ?: $history->action }}</p>
                            <p class="text-gray-400">{{ $history->created_at->format('d/m/Y H:i') }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
        @endif
    </div>
</div>

<!-- Cancel Modal -->
<div id="cancelModal" class="hidden fixed inset-0 z-50 overflow-y-auto">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="fixed inset-0 bg-black bg-opacity-50" onclick="document.getElementById('cancelModal').classList.add('hidden')"></div>
        <div class="relative bg-white rounded-2xl shadow-xl w-full max-w-sm z-10 p-6">
            <h3 class="text-lg font-bold text-gray-900 mb-2">ยืนยันการยกเลิก</h3>
            <p class="text-sm text-gray-600 mb-4">กรุณาระบุเหตุผลในการยกเลิกการจอง</p>
            <form method="POST" action="{{ route('booking.cancel', $booking->booking_no) }}">
                @csrf
                <textarea name="reason" required rows="3"
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-red-400 focus:border-red-400 mb-4"
                    placeholder="เหตุผลในการยกเลิก..."></textarea>
                <div class="flex gap-3">
                    <button type="button"
                        onclick="document.getElementById('cancelModal').classList.add('hidden')"
                        class="flex-1 bg-gray-100 hover:bg-gray-200 text-gray-700 font-medium py-2.5 rounded-xl text-sm transition-colors">
                        ไม่ยกเลิก
                    </button>
                    <button type="submit"
                        class="flex-1 bg-red-500 hover:bg-red-600 text-white font-semibold py-2.5 rounded-xl text-sm transition-colors">
                        ยืนยันยกเลิก
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

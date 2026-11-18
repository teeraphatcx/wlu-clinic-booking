@extends('layouts.app')

@section('title', 'เปลี่ยนวันนัดหมาย - ' . $booking->booking_no)

@section('content')
<div class="max-w-4xl mx-auto px-4 py-6" x-data="bookingCalendar({{ $month }}, {{ $year }}, {{ $service->id }}, {{ $service->requires_therapist_selection ? 'true' : 'false' }})">
    <div class="mb-6">
        <a href="{{ route('booking.show', $booking->booking_no) }}" class="inline-flex items-center gap-1 text-sm text-brand-600 hover:text-brand-700 mb-3">
            ← กลับ
        </a>
        <h1 class="text-xl font-bold text-brand-800">เปลี่ยนวันนัดหมาย</h1>
        <p class="text-sm text-gray-600 mt-1">รหัสการจอง: <span class="font-mono font-semibold">{{ $booking->booking_no }}</span></p>
    </div>

    <!-- Current Booking Info -->
    <div class="bg-orange-50 border border-orange-200 rounded-xl p-4 mb-6 text-sm">
        <p class="font-medium text-orange-800 mb-1">🔄 ข้อมูลการจองปัจจุบัน</p>
        <p class="text-orange-700">
            {{ $booking->service->name_th }} ·
            {{ \Carbon\Carbon::parse($booking->booking_date)->locale('th')->isoFormat('D MMM YYYY') }} ·
            {{ $booking->timeSlot?->label_th }}
            @if($booking->therapist)
                · {{ $booking->therapist->full_name }}
            @endif
        </p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Calendar (same as booking) -->
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
            <div class="flex items-center justify-between p-4 bg-brand-600 text-white">
                <button @click="prevMonth()" class="p-1 rounded hover:bg-brand-500">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                </button>
                <span class="font-semibold" x-text="getMonthName()"></span>
                <button @click="nextMonth()" class="p-1 rounded hover:bg-brand-500">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                </button>
            </div>
            <div class="grid grid-cols-7 bg-gray-50 border-b">
                @foreach(['อา','จ','อ','พ','พฤ','ศ','ส'] as $day)
                    <div class="p-2 text-center text-xs font-medium text-gray-500">{{ $day }}</div>
                @endforeach
            </div>
            <div class="p-3">
                <div class="grid grid-cols-7 gap-1">
                    <template x-for="i in firstDayOffset" :key="'empty-' + i">
                        <div class="h-9"></div>
                    </template>
                    <template x-for="dateInfo in calendarDates" :key="dateInfo.date">
                        <button @click="selectDate(dateInfo)" :disabled="dateInfo.status !== 'available'"
                            :class="getDateClass(dateInfo)"
                            class="h-9 w-full rounded-lg text-sm font-medium transition-all focus:outline-none">
                            <span x-text="new Date(dateInfo.date).getDate()"></span>
                        </button>
                    </template>
                </div>
            </div>
        </div>

        <!-- Slots & Submit -->
        <div>
            <div x-show="!selectedDate" class="bg-brand-50 border border-brand-100 rounded-xl p-6 text-center text-brand-600">
                <p class="font-medium">เลือกวันนัดหมายใหม่</p>
            </div>
            <div x-show="selectedDate" x-cloak>
                <h3 class="font-semibold text-gray-800 mb-3" x-text="'วัน ' + formatSelectedDate()"></h3>
                <div x-show="loadingSlots" class="text-center py-4 text-gray-400 text-sm">กำลังโหลด...</div>
                <div x-show="!loadingSlots && slots.length > 0" class="space-y-2">
                    <template x-for="slot in slots" :key="slot.id">
                        <button @click="selectSlot(slot)" :disabled="slot.status !== 'available'"
                            :class="getSlotClass(slot)"
                            class="w-full flex items-center justify-between p-3 rounded-lg border text-sm transition-all">
                            <span class="font-medium" x-text="slot.label_th || (slot.start_time + ' - ' + slot.end_time)"></span>
                            <span :class="slot.status === 'available' ? 'text-green-600' : 'text-gray-400'" class="text-xs"
                                x-text="slot.status === 'available' ? 'ว่าง ' + slot.remaining_capacity + ' คิว' : (slot.status === 'full' ? 'คิวเต็ม' : 'ปิดรับ')">
                            </span>
                        </button>
                    </template>
                </div>
                <div x-show="!loadingSlots && slots.length === 0" class="text-center py-4 text-gray-400 text-sm bg-gray-50 rounded-xl">
                    ไม่มีรอบเวลาว่าง
                </div>

                <!-- Therapist Selection (if required) -->
                <div x-show="requiresTherapist && selectedSlot && therapists.length > 0" class="mt-4">
                    <h4 class="font-medium text-gray-700 mb-2">เลือกหมอนวด</h4>
                    <div x-show="loadingTherapists" class="text-center py-3 text-gray-400 text-sm">กำลังโหลด...</div>
                    <div x-show="!loadingTherapists" class="space-y-2">
                        <template x-for="therapist in therapists" :key="therapist.id">
                            <button type="button"
                                @click="selectedTherapist = therapist"
                                :class="selectedTherapist && selectedTherapist.id === therapist.id ? 'bg-brand-50 border-brand-400' : 'bg-white border-gray-200 hover:border-brand-300'"
                                class="w-full flex items-center gap-3 p-3 rounded-lg border text-sm transition-all text-left">
                                <div class="w-10 h-10 overflow-hidden rounded-full bg-brand-100 flex items-center justify-center flex-shrink-0 font-semibold text-brand-700">
                                    <img x-show="therapist.photo_url" :src="therapist.photo_url" :alt="therapist.name" class="h-full w-full object-cover">
                                    <span x-show="!therapist.photo_url" x-text="therapist.name.charAt(0)"></span>
                                </div>
                                <div>
                                    <div class="font-medium text-gray-800" x-text="therapist.name"></div>
                                    <div x-show="therapist.nickname" class="text-xs text-gray-500" x-text="'(' + therapist.nickname + ')'"></div>
                                </div>
                                <div x-show="selectedTherapist && selectedTherapist.id === therapist.id" class="ml-auto text-brand-500">
                                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                    </svg>
                                </div>
                            </button>
                        </template>
                    </div>
                </div>

                <!-- Submit Reschedule -->
                <div x-show="selectedSlot && (!requiresTherapist || selectedTherapist)" class="mt-5">
                    <form method="POST" action="{{ route('booking.reschedule', $booking->booking_no) }}">
                        @csrf
                        <input type="hidden" name="new_date" :value="selectedDate">
                        <input type="hidden" name="time_slot_id" :value="selectedSlot ? selectedSlot.id : ''">
                        <input type="hidden" name="therapist_id" :value="selectedTherapist ? selectedTherapist.id : ''">
                        <button type="submit"
                            class="w-full bg-brand-600 hover:bg-brand-700 text-white font-semibold py-3 rounded-xl transition-colors">
                            ยืนยันเปลี่ยนวันนัดหมาย
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function bookingCalendar(month, year, serviceId, requiresTherapist) {
    return {
        month, year, serviceId, requiresTherapist,
        calendarDates: @json($calendarDates),
        selectedDate: null, selectedSlot: null, selectedTherapist: null,
        slots: [], therapists: [], loadingSlots: false, loadingTherapists: false,

        get firstDayOffset() {
            return new Date(this.year, this.month - 1, 1).getDay();
        },
        getMonthName() {
            const months = ['มกราคม','กุมภาพันธ์','มีนาคม','เมษายน','พฤษภาคม','มิถุนายน',
                           'กรกฎาคม','สิงหาคม','กันยายน','ตุลาคม','พฤศจิกายน','ธันวาคม'];
            return months[this.month - 1] + ' ' + (this.year + 543);
        },
        async prevMonth() {
            this.month--; if (this.month < 1) { this.month = 12; this.year--; }
            window.location.href = `?month=${this.month}&year=${this.year}`;
        },
        async nextMonth() {
            this.month++; if (this.month > 12) { this.month = 1; this.year++; }
            window.location.href = `?month=${this.month}&year=${this.year}`;
        },
        getDateClass(d) {
            if (this.selectedDate === d.date) return 'bg-brand-600 text-white';
            switch(d.status) {
                case 'available': return 'bg-green-50 text-green-800 hover:bg-green-100 border border-green-200 cursor-pointer';
                case 'full': return 'bg-red-50 text-red-400 cursor-not-allowed';
                default: return 'text-gray-300 cursor-not-allowed';
            }
        },
        async selectDate(d) {
            if (d.status !== 'available') return;
            this.selectedDate = d.date; this.selectedSlot = null;
            this.loadingSlots = true;
            const res = await fetch(`/booking/${this.serviceId}/slots?date=${this.selectedDate}`);
            const data = await res.json();
            this.slots = data.slots || [];
            this.loadingSlots = false;
        },
        async selectSlot(slot) {
            if (slot.status !== 'available') return;
            this.selectedSlot = slot;
            this.selectedTherapist = null;
            if (this.requiresTherapist) {
                this.loadingTherapists = true;
                try {
                    const res = await fetch(`/booking/${this.serviceId}/therapists?date=${this.selectedDate}&time_slot_id=${slot.id}`);
                    const data = await res.json();
                    this.therapists = data.therapists || [];
                } catch (e) {
                    this.therapists = [];
                } finally {
                    this.loadingTherapists = false;
                }
            }
        },
        getSlotClass(slot) {
            if (this.selectedSlot?.id === slot.id) return 'bg-brand-50 border-brand-400 text-brand-800';
            return slot.status === 'available' ? 'bg-white border-gray-200 hover:border-brand-300 cursor-pointer' : 'bg-gray-50 border-gray-100 text-gray-400 cursor-not-allowed';
        },
        formatSelectedDate() {
            if (!this.selectedDate) return '';
            const d = new Date(this.selectedDate);
            const days = ['อาทิตย์','จันทร์','อังคาร','พุธ','พฤหัสบดี','ศุกร์','เสาร์'];
            const months = ['ม.ค.','ก.พ.','มี.ค.','เม.ย.','พ.ค.','มิ.ย.','ก.ค.','ส.ค.','ก.ย.','ต.ค.','พ.ย.','ธ.ค.'];
            return `วัน${days[d.getDay()]}ที่ ${d.getDate()} ${months[d.getMonth()]} ${d.getFullYear() + 543}`;
        }
    }
}
</script>
@endpush
@endsection

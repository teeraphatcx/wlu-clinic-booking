@extends('layouts.app')

@section('title', 'จองคิว - ' . $service->name_th)

@section('content')
<div class="max-w-4xl mx-auto px-4 py-6" x-data="bookingCalendar({{ $month }}, {{ $year }}, {{ $service->id }}, {{ $service->requires_therapist_selection ? 'true' : 'false' }})">

    <!-- Header -->
    <div class="mb-6">
        <nav class="flex mb-3 text-sm">
            <ol class="flex items-center space-x-2">
                <li><a href="{{ route('home') }}" class="text-brand-600">หน้าหลัก</a></li>
                <li class="text-gray-400">/</li>
                <li><a href="{{ route('booking.index') }}" class="text-brand-600">จองคิว</a></li>
                <li class="text-gray-400">/</li>
                <li class="text-gray-700">{{ $service->name_th }}</li>
            </ol>
        </nav>
        <h1 class="text-xl font-bold text-brand-800">{{ $service->name_th }}</h1>
        <div class="flex items-center gap-3 mt-1 text-sm text-gray-600">
            <span>⏱ {{ $service->duration_minutes }} นาที</span>
            <span>฿{{ number_format($service->price, 0) }}</span>
        </div>
    </div>

    <!-- Step Indicator -->
    <div class="flex items-center mb-6 text-sm">
        <div class="flex items-center">
            <div class="flex items-center justify-center w-7 h-7 rounded-full bg-green-500 text-white font-bold text-xs">✓</div>
            <span class="ml-1.5 text-green-700 font-medium">เลือกบริการ</span>
        </div>
        <div class="w-10 h-0.5 bg-gray-300 mx-2"></div>
        <div class="flex items-center">
            <div class="flex items-center justify-center w-7 h-7 rounded-full bg-brand-600 text-white font-bold text-xs">2</div>
            <span class="ml-1.5 text-brand-700 font-medium">เลือกวัน-เวลา</span>
        </div>
        <div class="w-10 h-0.5 bg-gray-300 mx-2"></div>
        <div class="flex items-center">
            <div class="flex items-center justify-center w-7 h-7 rounded-full bg-gray-200 text-gray-500 font-bold text-xs">3</div>
            <span class="ml-1.5 text-gray-500">ยืนยัน</span>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Calendar -->
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
            <!-- Month Navigation -->
            <div class="flex items-center justify-between p-4 bg-brand-600 text-white">
                <button @click="prevMonth()" class="p-1 rounded hover:bg-brand-500 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                    </svg>
                </button>
                <span class="font-semibold" x-text="getMonthName()"></span>
                <button @click="nextMonth()" class="p-1 rounded hover:bg-brand-500 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                </button>
            </div>

            <!-- Day Headers -->
            <div class="grid grid-cols-7 bg-gray-50 border-b">
                @foreach(['อา','จ','อ','พ','พฤ','ศ','ส'] as $day)
                    <div class="p-2 text-center text-xs font-medium text-gray-500">{{ $day }}</div>
                @endforeach
            </div>

            <!-- Calendar Grid -->
            <div class="p-3">
                <div class="grid grid-cols-7 gap-1">
                    <!-- Empty cells for first day -->
                    <template x-for="i in firstDayOffset" :key="'empty-' + i">
                        <div class="h-9"></div>
                    </template>

                    <!-- Date cells -->
                    <template x-for="dateInfo in calendarDates" :key="dateInfo.date">
                        <button
                            @click="selectDate(dateInfo)"
                            :disabled="dateInfo.status !== 'available'"
                            :class="getDateClass(dateInfo)"
                            class="h-9 w-full rounded-lg text-sm font-medium transition-all relative focus:outline-none focus:ring-2 focus:ring-brand-400"
                            :title="getDateTitle(dateInfo)">
                            <span x-text="new Date(dateInfo.date).getDate()"></span>
                            <span x-show="dateInfo.status === 'available'" class="absolute bottom-0.5 left-1/2 -translate-x-1/2 w-1 h-1 rounded-full bg-green-500"></span>
                        </button>
                    </template>
                </div>

                <!-- Legend -->
                <div class="mt-4 flex flex-wrap gap-3 text-xs text-gray-600">
                    <div class="flex items-center gap-1.5">
                        <div class="w-5 h-5 rounded bg-green-100 border border-green-300 flex items-center justify-center">
                            <div class="w-1.5 h-1.5 rounded-full bg-green-500"></div>
                        </div>
                        <span>มีคิวว่าง</span>
                    </div>
                    <div class="flex items-center gap-1.5">
                        <div class="w-5 h-5 rounded bg-red-50 border border-red-200"></div>
                        <span>คิวเต็ม</span>
                    </div>
                    <div class="flex items-center gap-1.5">
                        <div class="w-5 h-5 rounded bg-gray-100"></div>
                        <span>ปิดทำการ</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Panel: Slots & Form -->
        <div>
            <!-- No date selected -->
            <div x-show="!selectedDate" class="bg-brand-50 border border-brand-100 rounded-xl p-6 text-center text-brand-600">
                <svg class="w-12 h-12 mx-auto mb-2 text-brand-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
                <p class="font-medium">กรุณาเลือกวันนัดหมาย</p>
            </div>

            <!-- Date selected -->
            <div x-show="selectedDate" x-cloak>
                <h3 class="font-semibold text-gray-800 mb-3">
                    วัน <span x-text="formatSelectedDate()"></span>
                </h3>

                <!-- Loading Slots -->
                <div x-show="loadingSlots" class="text-center py-6 text-gray-500">
                    <svg class="animate-spin w-6 h-6 mx-auto mb-2 text-brand-500" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                    </svg>
                    กำลังโหลดรอบเวลา...
                </div>

                <!-- Time Slots -->
                <div x-show="!loadingSlots && slots.length > 0" class="space-y-2">
                    <template x-for="slot in slots" :key="slot.id">
                        <button
                            @click="selectSlot(slot)"
                            :disabled="slot.status !== 'available'"
                            :class="getSlotClass(slot)"
                            class="w-full flex items-center justify-between p-3 rounded-lg border text-sm transition-all">
                            <div class="flex items-center gap-2">
                                <span class="font-medium" x-text="slot.label_th || (slot.start_time + ' - ' + slot.end_time)"></span>
                            </div>
                            <div class="flex items-center gap-2">
                                <span class="text-xs" :class="slot.status === 'available' ? 'text-green-600' : 'text-gray-400'"
                                    x-text="slot.status === 'available' ? 'ว่าง ' + slot.remaining_capacity + ' คิว' : (slot.status === 'full' ? 'คิวเต็ม' : 'ปิดรับจอง')">
                                </span>
                                <div class="w-2 h-2 rounded-full"
                                    :class="slot.status === 'available' ? 'bg-green-500' : 'bg-gray-300'"></div>
                            </div>
                        </button>
                    </template>
                </div>

                <!-- No slots -->
                <div x-show="!loadingSlots && slots.length === 0" class="text-center py-6 text-gray-500 bg-gray-50 rounded-xl">
                    ไม่มีรอบเวลาว่างในวันนี้
                </div>

                <!-- Therapist Selection (if required) -->
                <div x-show="requiresTherapist && selectedSlot && therapists.length > 0" class="mt-4">
                    <h4 class="font-medium text-gray-700 mb-2">เลือกหมอนวด</h4>
                    <div x-show="loadingTherapists" class="text-center py-3 text-gray-400 text-sm">กำลังโหลด...</div>
                    <div x-show="!loadingTherapists" class="space-y-2">
                        <template x-for="therapist in therapists" :key="therapist.id">
                            <button
                                @click="selectTherapist(therapist)"
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

                <!-- Continue Button -->
                <div x-show="selectedSlot && (!requiresTherapist || selectedTherapist)" class="mt-5">
                    <form method="GET" action="{{ route('booking.store') }}" id="dateSelectionForm">
                        <!-- Hidden fields populated by Alpine -->
                        <input type="hidden" name="service_id" :value="{{ $service->id }}">
                        <input type="hidden" name="booking_date" :value="selectedDate">
                        <input type="hidden" name="time_slot_id" :value="selectedSlot ? selectedSlot.id : ''">
                        <input type="hidden" name="therapist_id" :value="selectedTherapist ? selectedTherapist.id : ''">
                    </form>
                    <button @click="proceedToForm()"
                        class="w-full bg-brand-600 hover:bg-brand-700 text-white font-semibold py-3 px-6 rounded-xl transition-colors shadow-sm">
                        ถัดไป — กรอกข้อมูลการจอง →
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Booking Form Modal -->
<div x-data="{ open: false, formData: {} }" @open-booking-form.window="open = true; formData = $event.detail" x-show="open" x-cloak
    class="fixed inset-0 z-50 overflow-y-auto" aria-modal="true">
    <div class="flex items-end sm:items-center justify-center min-h-screen p-4">
        <div class="fixed inset-0 bg-black bg-opacity-50" @click="open = false"></div>
        <div class="relative bg-white rounded-2xl shadow-xl w-full max-w-lg mx-auto z-10">
            <div class="p-6">
                <div class="flex items-center justify-between mb-5">
                    <h2 class="text-lg font-bold text-gray-900">กรอกข้อมูลการจอง</h2>
                    <button @click="open = false" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                <!-- Summary -->
                <div class="bg-brand-50 rounded-lg p-4 mb-5 text-sm space-y-1">
                    <div class="font-medium text-brand-800">{{ $service->name_th }}</div>
                    <div class="text-brand-600" x-text="'วันที่: ' + (formData.date || '-')"></div>
                    <div class="text-brand-600" x-text="'เวลา: ' + (formData.slotLabel || '-')"></div>
                    <div x-show="formData.therapistName" class="text-brand-600" x-text="'หมอนวด: ' + formData.therapistName"></div>
                    <div class="font-medium text-green-700">ราคา: {{ number_format($service->price, 0) }} บาท</div>
                </div>

                <form method="POST" action="{{ route('booking.store') }}">
                    @csrf
                    <input type="hidden" name="service_id" value="{{ $service->id }}">
                    <input type="hidden" name="booking_date" :value="formData.rawDate">
                    <input type="hidden" name="time_slot_id" :value="formData.slotId">
                    <input type="hidden" name="therapist_id" :value="formData.therapistId">

                    <div class="space-y-4">
                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">ชื่อ <span class="text-red-500">*</span></label>
                                <input type="text" name="first_name" required
                                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-brand-400 focus:border-brand-400"
                                    placeholder="ชื่อ">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">นามสกุล <span class="text-red-500">*</span></label>
                                <input type="text" name="last_name" required
                                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-brand-400 focus:border-brand-400"
                                    placeholder="นามสกุล">
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">เบอร์โทรศัพท์ <span class="text-red-500">*</span></label>
                            <input type="tel" name="phone" required pattern="0[0-9]{8,9}"
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-brand-400 focus:border-brand-400"
                                placeholder="0812345678">
                            <p class="text-xs text-gray-500 mt-1">ใช้สำหรับตรวจสอบสถานะการจอง</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">อีเมล (ไม่บังคับ)</label>
                            <input type="email" name="email"
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-brand-400 focus:border-brand-400"
                                placeholder="example@email.com">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">หมายเหตุ (ไม่บังคับ)</label>
                            <textarea name="notes" rows="2"
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-brand-400 focus:border-brand-400"
                                placeholder="ระบุอาการหรือข้อมูลเพิ่มเติม..."></textarea>
                        </div>

                        <!-- PDPA -->
                        <div class="border border-orange-200 bg-orange-50 rounded-lg p-3">
                            <label class="flex items-start gap-2 cursor-pointer">
                                <input type="checkbox" name="pdpa_accepted" value="1" required class="mt-0.5 w-4 h-4 rounded border-gray-300 text-brand-600 focus:ring-brand-500 flex-shrink-0">
                                <span class="text-xs text-gray-700">
                                    ฉันยินยอมให้คลินิกเก็บรวบรวมและประมวลผลข้อมูลส่วนบุคคลของฉัน
                                    เพื่อวัตถุประสงค์ในการให้บริการ ตามนโยบายความเป็นส่วนตัว
                                    <a href="{{ route('privacy') }}" target="_blank" class="text-brand-600 underline">อ่านเพิ่มเติม</a>
                                </span>
                            </label>
                        </div>
                    </div>

                    <button type="submit" class="mt-5 w-full bg-brand-600 hover:bg-brand-700 text-white font-semibold py-3 rounded-xl transition-colors">
                        ยืนยันการจองคิว
                    </button>
                </form>
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
        selectedDate: null,
        selectedSlot: null,
        selectedTherapist: null,
        slots: [],
        therapists: [],
        loadingSlots: false,
        loadingTherapists: false,

        get firstDayOffset() {
            const d = new Date(this.year, this.month - 1, 1);
            return d.getDay();
        },

        getMonthName() {
            const months = ['มกราคม','กุมภาพันธ์','มีนาคม','เมษายน','พฤษภาคม','มิถุนายน',
                           'กรกฎาคม','สิงหาคม','กันยายน','ตุลาคม','พฤศจิกายน','ธันวาคม'];
            return months[this.month - 1] + ' ' + (this.year + 543);
        },

        async prevMonth() {
            this.month--;
            if (this.month < 1) { this.month = 12; this.year--; }
            await this.loadMonth();
        },

        async nextMonth() {
            this.month++;
            if (this.month > 12) { this.month = 1; this.year++; }
            await this.loadMonth();
        },

        async loadMonth() {
            const res = await fetch(`/booking/${this.serviceId}/slots?month=${this.month}&year=${this.year}`);
            // For now reload page with month/year params
            window.location.href = `?month=${this.month}&year=${this.year}`;
        },

        getDateClass(dateInfo) {
            const isSelected = this.selectedDate === dateInfo.date;
            if (isSelected) return 'bg-brand-600 text-white shadow-md scale-105';
            switch(dateInfo.status) {
                case 'available': return 'bg-green-50 text-green-800 hover:bg-green-100 border border-green-200 cursor-pointer';
                case 'full': return 'bg-red-50 text-red-400 cursor-not-allowed border border-red-100';
                case 'holiday': return 'bg-yellow-50 text-yellow-500 cursor-not-allowed';
                case 'past': return 'text-gray-300 cursor-not-allowed';
                default: return 'bg-gray-50 text-gray-400 cursor-not-allowed';
            }
        },

        getDateTitle(dateInfo) {
            if (dateInfo.holiday_name) return 'วันหยุด: ' + dateInfo.holiday_name;
            if (dateInfo.status === 'full') return 'คิวเต็ม';
            if (dateInfo.status === 'closed') return 'ปิดทำการ';
            return '';
        },

        async selectDate(dateInfo) {
            if (dateInfo.status !== 'available') return;
            this.selectedDate = dateInfo.date;
            this.selectedSlot = null;
            this.selectedTherapist = null;
            this.therapists = [];
            await this.loadSlots();
        },

        async loadSlots() {
            this.loadingSlots = true;
            try {
                const res = await fetch(`/booking/${this.serviceId}/slots?date=${this.selectedDate}`);
                const data = await res.json();
                this.slots = data.slots || [];
            } catch(e) {
                this.slots = [];
            } finally {
                this.loadingSlots = false;
            }
        },

        async selectSlot(slot) {
            if (slot.status !== 'available') return;
            this.selectedSlot = slot;
            this.selectedTherapist = null;
            if (this.requiresTherapist) {
                await this.loadTherapists();
            }
        },

        async loadTherapists() {
            this.loadingTherapists = true;
            try {
                const res = await fetch(`/booking/${this.serviceId}/therapists?date=${this.selectedDate}&time_slot_id=${this.selectedSlot.id}`);
                const data = await res.json();
                this.therapists = data.therapists || [];
            } finally {
                this.loadingTherapists = false;
            }
        },

        selectTherapist(t) {
            this.selectedTherapist = t;
        },

        formatSelectedDate() {
            if (!this.selectedDate) return '';
            const d = new Date(this.selectedDate);
            const days = ['อาทิตย์','จันทร์','อังคาร','พุธ','พฤหัสบดี','ศุกร์','เสาร์'];
            const months = ['ม.ค.','ก.พ.','มี.ค.','เม.ย.','พ.ค.','มิ.ย.','ก.ค.','ส.ค.','ก.ย.','ต.ค.','พ.ย.','ธ.ค.'];
            return `วัน${days[d.getDay()]}ที่ ${d.getDate()} ${months[d.getMonth()]} ${d.getFullYear() + 543}`;
        },

        getSlotClass(slot) {
            const isSelected = this.selectedSlot && this.selectedSlot.id === slot.id;
            if (isSelected) return 'bg-brand-50 border-brand-400 text-brand-800';
            if (slot.status === 'available') return 'bg-white border-gray-200 hover:border-brand-300 text-gray-800 cursor-pointer';
            return 'bg-gray-50 border-gray-100 text-gray-400 cursor-not-allowed';
        },

        proceedToForm() {
            const detail = {
                date: this.formatSelectedDate(),
                rawDate: this.selectedDate,
                slotId: this.selectedSlot?.id,
                slotLabel: this.selectedSlot?.label_th || (this.selectedSlot ? this.selectedSlot.start_time + ' - ' + this.selectedSlot.end_time : ''),
                therapistId: this.selectedTherapist?.id || null,
                therapistName: this.selectedTherapist?.name || null,
            };
            window.dispatchEvent(new CustomEvent('open-booking-form', { detail }));
        }
    }
}
</script>
@endpush
@endsection

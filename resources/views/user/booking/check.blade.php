@extends('layouts.app')

@section('title', 'ตรวจสอบ / จัดการการจอง')

@section('content')
<div class="max-w-lg mx-auto px-4 py-8">
    <div class="text-center mb-8">
        <h1 class="text-2xl font-bold text-brand-800 mb-2">ตรวจสอบสถานะการจอง</h1>
        <p class="text-gray-600 text-sm">กรอกรหัสการจองและเบอร์โทรศัพท์ที่ใช้จอง</p>
    </div>

    <!-- Error Messages -->
    @if($errors->any())
        <div class="mb-5 bg-red-50 border border-red-200 rounded-xl p-4">
            @foreach($errors->all() as $error)
                <p class="text-red-700 text-sm">⚠️ {{ $error }}</p>
            @endforeach
        </div>
    @endif

    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6">
        <form method="POST" action="{{ route('booking.check') }}">
            @csrf
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">รหัสการจอง</label>
                    <input type="text" name="booking_no" value="{{ old('booking_no') }}" required
                        placeholder="เช่น WLU-241110-0001"
                        class="w-full border border-gray-300 rounded-xl px-4 py-3 font-mono text-sm focus:ring-2 focus:ring-brand-400 focus:border-brand-400 uppercase"
                        oninput="this.value = this.value.toUpperCase()">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">เบอร์โทรศัพท์ที่ใช้จอง</label>
                    <input type="tel" name="phone" value="{{ old('phone') }}" required
                        placeholder="0812345678"
                        class="w-full border border-gray-300 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-brand-400 focus:border-brand-400">
                </div>
                <button type="submit"
                    class="w-full bg-brand-600 hover:bg-brand-700 text-white font-semibold py-3 rounded-xl transition-colors">
                    🔍 ค้นหาการจอง
                </button>
            </div>
        </form>

        <div class="mt-5 pt-5 border-t border-gray-100 text-center">
            <p class="text-sm text-gray-500">ต้องการจองคิวใหม่?</p>
            <a href="{{ route('booking.index') }}" class="text-brand-600 hover:text-brand-700 text-sm font-medium">
                คลิกที่นี่เพื่อจองคิว →
            </a>
        </div>
    </div>
</div>
@endsection

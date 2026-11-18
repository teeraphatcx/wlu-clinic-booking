@extends('admin.layout')
@section('title', 'จัดการข้อมูลผู้รับบริการ')

@section('content')
<div class="mb-6 flex flex-col justify-between gap-4 sm:flex-row sm:items-center">
    <div>
        <p class="text-xs font-semibold uppercase tracking-wider text-brand-600">Customer Management</p>
        <h1 class="text-2xl font-bold text-slate-900 mt-0.5">จัดการข้อมูลผู้รับบริการ</h1>
        <p class="mt-1 text-sm text-slate-500">ฐานข้อมูลผู้ป่วย/ผู้รับบริการ ประวัติคิว และข้อมูลสุขภาพที่จำเป็น</p>
    </div>
</div>

<!-- Search & Filter Bar -->
<section class="mb-6 rounded-2xl bg-white p-5 shadow-sm border border-slate-100">
    <form method="get" action="{{ route('admin.customers.index') }}" class="grid gap-3 sm:grid-cols-[1fr_200px_auto_auto] items-end">
        <div>
            <label class="mb-1 block text-xs font-semibold text-slate-600">ค้นหา</label>
            <input name="search" value="{{ request('search') }}" placeholder="ชื่อ, นามสกุล หรือเบอร์โทรศัพท์" class="w-full rounded-xl border-slate-300 text-sm focus:border-brand-500 focus:ring-brand-200">
        </div>
        <div>
            <label class="mb-1 block text-xs font-semibold text-slate-600">สถานะบัญชี</label>
            <select name="state" class="w-full rounded-xl border-slate-300 text-sm focus:border-brand-500 focus:ring-brand-200">
                <option value="">ทุกสถานะ</option>
                <option value="1" @selected(request('state') === '1')>เปิดใช้งาน</option>
                <option value="0" @selected(request('state') === '0')>ระงับการใช้งาน</option>
            </select>
        </div>
        <button type="submit" class="rounded-xl bg-brand-700 px-5 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-brand-800 transition-colors">
            ค้นหา
        </button>
        @if(request()->hasAny(['search', 'state']))
            <a href="{{ route('admin.customers.index') }}" class="rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-center text-sm font-semibold text-slate-600 hover:bg-slate-50">
                ล้างตัวกรอง
            </a>
        @endif
    </form>
</section>

<!-- Add Customer Accordion -->
<details class="group mb-6 rounded-2xl bg-white border border-slate-200 shadow-sm overflow-hidden" {{ $errors->any() ? 'open' : '' }}>
    <summary class="flex cursor-pointer items-center justify-between p-5 font-semibold text-slate-800 hover:bg-slate-50 transition-colors select-none">
        <div class="flex items-center gap-3">
            <span class="grid h-8 w-8 place-items-center rounded-xl bg-brand-50 text-brand-700 font-bold text-lg">＋</span>
            <div>
                <p class="font-bold text-slate-900 text-base">เพิ่มข้อมูลผู้รับบริการใหม่</p>
                <p class="text-xs text-slate-500 font-normal">บันทึกประวัติผู้รับบริการรายใหม่เข้าสู่ระบบคลินิก</p>
            </div>
        </div>
        <span class="text-xs text-brand-700 font-semibold group-open:rotate-180 transition-transform duration-200">▼</span>
    </summary>

    <div class="border-t border-slate-100 p-5 sm:p-6 bg-slate-50/50">
        <form method="post" action="{{ route('admin.customers.store') }}" class="space-y-4">
            @csrf
            <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                <div>
                    <label class="mb-1 block text-xs font-semibold text-slate-700">ชื่อจริง <span class="text-rose-500">*</span></label>
                    <input name="first_name" value="{{ old('first_name') }}" required placeholder="ชื่อ" class="w-full rounded-xl border-slate-300 text-sm focus:border-brand-500 focus:ring-brand-200">
                </div>
                <div>
                    <label class="mb-1 block text-xs font-semibold text-slate-700">นามสกุล <span class="text-rose-500">*</span></label>
                    <input name="last_name" value="{{ old('last_name') }}" required placeholder="นามสกุล" class="w-full rounded-xl border-slate-300 text-sm focus:border-brand-500 focus:ring-brand-200">
                </div>
                <div>
                    <label class="mb-1 block text-xs font-semibold text-slate-700">เบอร์โทรศัพท์ <span class="text-rose-500">*</span></label>
                    <input name="phone" value="{{ old('phone') }}" required placeholder="0812345678" class="w-full rounded-xl border-slate-300 text-sm focus:border-brand-500 focus:ring-brand-200">
                </div>
                <div>
                    <label class="mb-1 block text-xs font-semibold text-slate-700">อีเมล</label>
                    <input name="email" type="email" value="{{ old('email') }}" placeholder="example@email.com" class="w-full rounded-xl border-slate-300 text-sm focus:border-brand-500 focus:ring-brand-200">
                </div>
            </div>

            <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                <div>
                    <label class="mb-1 block text-xs font-semibold text-slate-700">HN (Hospital Number)</label>
                    <input name="hn" value="{{ old('hn') }}" placeholder="เช่น 66/01234" class="w-full rounded-xl border-slate-300 text-sm focus:border-brand-500 focus:ring-brand-200">
                </div>
                <div>
                    <label class="mb-1 block text-xs font-semibold text-slate-700">เพศ <span class="text-rose-500">*</span></label>
                    <select name="gender" class="w-full rounded-xl border-slate-300 text-sm focus:border-brand-500 focus:ring-brand-200">
                        <option value="other" @selected(old('gender') === 'other')>ไม่ระบุเพศ</option>
                        <option value="female" @selected(old('gender') === 'female')>หญิง</option>
                        <option value="male" @selected(old('gender') === 'male')>ชาย</option>
                    </select>
                </div>
                <div>
                    <label class="mb-1 block text-xs font-semibold text-slate-700">วัน/เดือน/ปี เกิด</label>
                    <input name="birthdate" type="date" value="{{ old('birthdate') }}" class="w-full rounded-xl border-slate-300 text-sm focus:border-brand-500 focus:ring-brand-200">
                </div>
                <div class="flex items-center pt-6">
                    <label class="flex items-center gap-2.5 text-sm font-medium text-slate-800 cursor-pointer">
                        <input type="checkbox" name="is_active" value="1" checked class="h-4 w-4 rounded border-slate-300 text-brand-600 focus:ring-brand-500">
                        <span>เปิดใช้งานบัญชี</span>
                    </label>
                </div>
            </div>

            <div class="grid gap-4 sm:grid-cols-2">
                <div>
                    <label class="mb-1 block text-xs font-semibold text-slate-700">ที่อยู่</label>
                    <textarea name="address" rows="2" placeholder="ที่อยู่ปัจจุบัน..." class="w-full rounded-xl border-slate-300 text-sm focus:border-brand-500 focus:ring-brand-200">{{ old('address') }}</textarea>
                </div>
                <div>
                    <label class="mb-1 block text-xs font-semibold text-slate-700">ข้อมูลที่จำเป็นต่อการให้บริการ / ข้อห้าม</label>
                    <textarea name="notes" rows="2" placeholder="เช่น โรคประจำตัว, ประวัติการแพ้ยา, อาการที่ไม่ควรนวด..." class="w-full rounded-xl border-slate-300 text-sm focus:border-brand-500 focus:ring-brand-200">{{ old('notes') }}</textarea>
                </div>
            </div>

            <div class="pt-2 flex justify-end">
                <button type="submit" class="rounded-xl bg-brand-700 px-6 py-2.5 text-sm font-semibold text-white shadow-md hover:bg-brand-800 transition-colors">
                    บันทึกข้อมูลผู้รับบริการ
                </button>
            </div>
        </form>
    </div>
</details>

<!-- Customers Table -->
<section class="rounded-2xl bg-white shadow-sm border border-slate-100 overflow-hidden">
    <div class="px-5 py-4 border-b border-slate-100">
        <h2 class="font-bold text-slate-900 text-base">รายชื่อผู้รับบริการ</h2>
        <p class="text-xs text-slate-500 mt-0.5">พบ {{ number_format($customers->total()) }} รายการ</p>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full min-w-[850px] text-left text-sm">
            <thead class="bg-slate-50 text-slate-600">
                <tr>
                    <th class="px-5 py-3.5">ผู้รับบริการ</th>
                    <th>เบอร์โทรศัพท์</th>
                    <th>HN</th>
                    <th>จำนวนการจอง</th>
                    <th>เข้ารับบริการแล้ว</th>
                    <th>สถานะ</th>
                    <th class="px-5 text-right">การจัดการ</th>
                </tr>
            </thead>
            <tbody>
                @forelse($customers as $customer)
                    <tr class="border-t border-slate-100 align-top hover:bg-slate-50/70 transition-colors">
                        <td class="px-5 py-4">
                            <a href="{{ route('admin.customers.show', $customer) }}" class="font-bold text-brand-700 hover:text-brand-900 hover:underline">
                                {{ $customer->full_name }}
                            </a>
                            @if($customer->email)
                                <p class="text-xs text-slate-400 mt-0.5">{{ $customer->email }}</p>
                            @endif

                            <!-- Edit Details Toggle -->
                            <details class="group mt-2">
                                <summary class="cursor-pointer text-xs font-semibold text-slate-500 hover:text-brand-700 select-none">
                                    แก้ไขข้อมูล ▼
                                </summary>
                                <div class="mt-2 p-4 rounded-xl bg-slate-50 border border-slate-200 text-left max-w-xl">
                                    <form method="post" action="{{ route('admin.customers.update', $customer) }}" class="space-y-3">
                                        @csrf
                                        @method('PUT')
                                        <div class="grid gap-2 sm:grid-cols-2">
                                            <div>
                                                <label class="block text-[10px] text-slate-500">ชื่อ</label>
                                                <input name="first_name" value="{{ $customer->first_name }}" required class="w-full rounded border-slate-300 text-xs">
                                            </div>
                                            <div>
                                                <label class="block text-[10px] text-slate-500">นามสกุล</label>
                                                <input name="last_name" value="{{ $customer->last_name }}" required class="w-full rounded border-slate-300 text-xs">
                                            </div>
                                            <div>
                                                <label class="block text-[10px] text-slate-500">เบอร์โทร</label>
                                                <input name="phone" value="{{ $customer->phone }}" required class="w-full rounded border-slate-300 text-xs">
                                            </div>
                                            <div>
                                                <label class="block text-[10px] text-slate-500">HN</label>
                                                <input name="hn" value="{{ $customer->hn }}" class="w-full rounded border-slate-300 text-xs">
                                            </div>
                                            <div>
                                                <label class="block text-[10px] text-slate-500">เพศ</label>
                                                <select name="gender" class="w-full rounded border-slate-300 text-xs">
                                                    <option value="other" @selected($customer->gender === 'other')>ไม่ระบุ</option>
                                                    <option value="female" @selected($customer->gender === 'female')>หญิง</option>
                                                    <option value="male" @selected($customer->gender === 'male')>ชาย</option>
                                                </select>
                                            </div>
                                            <div>
                                                <label class="block text-[10px] text-slate-500">วันเกิด</label>
                                                <input name="birthdate" type="date" value="{{ $customer->birthdate?->format('Y-m-d') }}" class="w-full rounded border-slate-300 text-xs">
                                            </div>
                                        </div>
                                        <button type="submit" class="rounded-lg bg-brand-700 px-4 py-1.5 text-xs font-semibold text-white hover:bg-brand-800">
                                            บันทึกการแก้ไข
                                        </button>
                                    </form>
                                </div>
                            </details>
                        </td>
                        <td class="py-4">
                            <a href="tel:{{ $customer->phone }}" class="font-medium text-slate-800 hover:text-brand-700">
                                {{ $customer->phone }}
                            </a>
                        </td>
                        <td class="py-4 font-mono text-xs text-slate-600">
                            {{ $customer->hn ?: '—' }}
                        </td>
                        <td class="py-4 font-semibold text-slate-800">
                            {{ $customer->bookings_count }}
                        </td>
                        <td class="py-4 font-semibold text-emerald-700">
                            {{ $customer->completed_bookings_count }}
                        </td>
                        <td class="py-4">
                            <span class="inline-block rounded-full px-2.5 py-0.5 text-xs font-semibold {{ $customer->is_active ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-rose-50 text-rose-700 border border-rose-200' }}">
                                {{ $customer->is_active ? 'เปิดใช้งาน' : 'ระงับการใช้' }}
                            </span>
                        </td>
                        <td class="px-5 py-4 text-right space-x-2 whitespace-nowrap">
                            <a href="{{ route('admin.customers.show', $customer) }}" class="text-xs font-semibold text-brand-700 hover:underline">
                                ประวัติคิว
                            </a>
                            <form method="post" action="{{ route('admin.customers.status', $customer) }}" class="inline-block">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="text-xs font-semibold {{ $customer->is_active ? 'text-rose-600 hover:text-rose-800' : 'text-emerald-600 hover:text-emerald-800' }} hover:underline">
                                    {{ $customer->is_active ? 'ระงับ' : 'เปิดใช้' }}
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="p-10 text-center text-slate-400 text-sm">
                            ไม่พบข้อมูลผู้รับบริการ
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($customers->hasPages())
        <div class="border-t border-slate-100 px-5 py-4">
            {{ $customers->links() }}
        </div>
    @endif
</section>
@endsection

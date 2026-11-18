@extends('admin.layout')
@section('title', 'จัดการหัตถการ')

@section('content')
<div class="mb-6 flex flex-col justify-between gap-4 sm:flex-row sm:items-center">
    <div>
        <p class="text-xs font-semibold uppercase tracking-wider text-brand-600">Service Management</p>
        <h1 class="text-2xl font-bold text-slate-900 mt-0.5">จัดการบริการหัตถการ</h1>
        <p class="mt-1 text-sm text-slate-500">กำหนดรายการหัตถการแพทย์แผนไทย อัตราค่าบริการ ระยะเวลา และแพทย์ผู้ให้บริการ</p>
    </div>
</div>

<!-- Add Service Accordion -->
<details class="group mb-6 rounded-2xl bg-white border border-slate-200 shadow-sm overflow-hidden" {{ $errors->any() ? 'open' : '' }}>
    <summary class="flex cursor-pointer items-center justify-between p-5 font-semibold text-slate-800 hover:bg-slate-50 transition-colors select-none">
        <div class="flex items-center gap-3">
            <span class="grid h-8 w-8 place-items-center rounded-xl bg-brand-50 text-brand-700 font-bold text-lg">＋</span>
            <div>
                <p class="font-bold text-slate-900 text-base">เพิ่มหัตถการใหม่</p>
                <p class="text-xs text-slate-500 font-normal">สร้างบริการตรวจรักษา บำบัด หรือฟื้นฟูสุขภาพ</p>
            </div>
        </div>
        <span class="text-xs text-brand-700 font-semibold group-open:rotate-180 transition-transform duration-200">▼</span>
    </summary>

    <div class="border-t border-slate-100 p-5 sm:p-6 bg-slate-50/50">
        <form method="post" action="{{ route('admin.services.store') }}" enctype="multipart/form-data" class="space-y-4">
            @csrf
            <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                <div>
                    <label class="mb-1 block text-xs font-semibold text-slate-700">รหัสบริการ <span class="text-rose-500">*</span></label>
                    <input name="code" value="{{ old('code') }}" required placeholder="เช่น TTM-MASSAGE-60" class="w-full rounded-xl border-slate-300 text-sm focus:border-brand-500 focus:ring-brand-200">
                </div>
                <div>
                    <label class="mb-1 block text-xs font-semibold text-slate-700">ชื่อหัตถการ (ไทย) <span class="text-rose-500">*</span></label>
                    <input name="name_th" value="{{ old('name_th') }}" required placeholder="เช่น นวดรักษาโรค" class="w-full rounded-xl border-slate-300 text-sm focus:border-brand-500 focus:ring-brand-200">
                </div>
                <div>
                    <label class="mb-1 block text-xs font-semibold text-slate-700">ชื่อหัตถการ (อังกฤษ)</label>
                    <input name="name_en" value="{{ old('name_en') }}" placeholder="เช่น Therapeutic Massage" class="w-full rounded-xl border-slate-300 text-sm focus:border-brand-500 focus:ring-brand-200">
                </div>
                <div>
                    <label class="mb-1 block text-xs font-semibold text-slate-700">ราคา (บาท) <span class="text-rose-500">*</span></label>
                    <input name="price" type="number" min="0" step="0.01" value="{{ old('price', 0) }}" required class="w-full rounded-xl border-slate-300 text-sm focus:border-brand-500 focus:ring-brand-200">
                </div>
            </div>

            <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                <div>
                    <label class="mb-1 block text-xs font-semibold text-slate-700">ระยะเวลา (นาที) <span class="text-rose-500">*</span></label>
                    <input name="duration_minutes" type="number" min="5" value="{{ old('duration_minutes', 60) }}" required class="w-full rounded-xl border-slate-300 text-sm focus:border-brand-500 focus:ring-brand-200">
                </div>
                <div>
                    <label class="mb-1 block text-xs font-semibold text-slate-700">ลำดับการแสดงผล</label>
                    <input name="sort_order" type="number" value="{{ old('sort_order', 0) }}" class="w-full rounded-xl border-slate-300 text-sm focus:border-brand-500 focus:ring-brand-200">
                </div>
                <div class="sm:col-span-2">
                    <label class="mb-1 block text-xs font-semibold text-slate-700">รูปภาพประกอบบริการ</label>
                    <input name="image" type="file" accept="image/png,image/jpeg,image/webp" class="w-full rounded-xl border border-slate-300 bg-white p-2 text-xs file:mr-3 file:rounded-md file:border-0 file:bg-brand-50 file:px-3 file:py-1 file:font-medium file:text-brand-700">
                </div>
            </div>

            <div>
                <label class="mb-1 block text-xs font-semibold text-slate-700">รายละเอียดหัตถการ</label>
                <textarea name="description" rows="2" placeholder="อธิบายขั้นตอน ข้อบ่งใช้ หรือการเตรียมตัวก่อนรับบริการ..." class="w-full rounded-xl border-slate-300 text-sm focus:border-brand-500 focus:ring-brand-200">{{ old('description') }}</textarea>
            </div>

            <div class="grid gap-4 sm:grid-cols-2">
                <div>
                    <label class="mb-1.5 block text-xs font-semibold text-slate-700">หมอนวด / แพทย์ผู้ให้บริการ</label>
                    <select multiple name="therapist_ids[]" class="min-h-28 w-full rounded-xl border-slate-300 text-sm focus:border-brand-500 focus:ring-brand-200 p-2">
                        @foreach($therapists as $therapist)
                            <option value="{{ $therapist->id }}">
                                {{ $therapist->displayName }} ({{ $therapist->code }})
                            </option>
                        @endforeach
                    </select>
                    <p class="mt-1 text-[11px] text-slate-400">กด Ctrl (หรือ Cmd) ค้างไว้เพื่อเลือกหลายคน</p>
                </div>
                <div class="flex flex-col justify-center space-y-3 pt-2">
                    <label class="flex items-center gap-2.5 text-sm font-medium text-slate-800 cursor-pointer">
                        <input type="checkbox" name="requires_therapist_selection" value="1" class="h-4 w-4 rounded border-slate-300 text-brand-600 focus:ring-brand-500">
                        <span>จำเป็นต้องให้ผู้รับบริการเลือกแพทย์แผนไทย</span>
                    </label>
                    <label class="flex items-center gap-2.5 text-sm font-medium text-slate-800 cursor-pointer">
                        <input type="checkbox" name="is_active" value="1" checked class="h-4 w-4 rounded border-slate-300 text-brand-600 focus:ring-brand-500">
                        <span>เปิดให้บริการทันที</span>
                    </label>
                </div>
            </div>

            <div class="pt-2 flex justify-end">
                <button type="submit" class="rounded-xl bg-brand-700 px-6 py-2.5 text-sm font-semibold text-white shadow-md hover:bg-brand-800 transition-colors">
                    บันทึกหัตถการ
                </button>
            </div>
        </form>
    </div>
</details>

<!-- Services List -->
<div class="space-y-4">
    @forelse($services as $service)
        <div class="rounded-2xl bg-white border border-slate-200 shadow-sm overflow-hidden">
            <details class="group">
                <summary class="flex cursor-pointer items-center justify-between p-5 hover:bg-slate-50 transition-colors select-none">
                    <div class="flex items-center gap-4">
                        @if($service->image)
                            <img src="{{ asset('storage/'.$service->image) }}" alt="{{ $service->name_th }}" class="h-14 w-14 rounded-xl object-cover border border-slate-200 shrink-0">
                        @else
                            <div class="grid h-14 w-14 shrink-0 place-items-center rounded-xl bg-brand-50 text-brand-700 font-bold text-lg">
                                ✚
                            </div>
                        @endif
                        <div>
                            <div class="flex items-center gap-2">
                                <h3 class="font-bold text-slate-900 text-base">{{ $service->name_th }}</h3>
                                <span class="font-mono text-xs bg-slate-100 text-slate-600 px-2 py-0.5 rounded">
                                    {{ $service->code }}
                                </span>
                            </div>
                            <div class="mt-1 flex flex-wrap items-center gap-3 text-xs text-slate-500">
                                <span>⏱ <strong>{{ $service->duration_minutes }}</strong> นาที</span>
                                <span>•</span>
                                <span class="text-brand-800 font-semibold">฿{{ number_format($service->price, 2) }}</span>
                                <span>•</span>
                                <span>แพทย์ผู้ให้บริการ {{ $service->therapists->count() }} ท่าน</span>
                            </div>
                        </div>
                    </div>
                    <div class="flex items-center gap-3">
                        <span class="rounded-full px-3 py-1 text-xs font-semibold {{ $service->is_active ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-slate-100 text-slate-500 border border-slate-200' }}">
                            {{ $service->is_active ? 'เปิดให้บริการ' : 'ปิดชั่วคราว' }}
                        </span>
                        <span class="text-xs text-brand-700 font-semibold group-open:rotate-180 transition-transform duration-200">
                            แก้ไข ▼
                        </span>
                    </div>
                </summary>

                <!-- Edit Form -->
                <div class="border-t border-slate-100 p-5 sm:p-6 bg-slate-50/50">
                    <form method="post" action="{{ route('admin.services.update', $service) }}" enctype="multipart/form-data" class="space-y-4">
                        @csrf
                        @method('PUT')
                        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                            <div>
                                <label class="mb-1 block text-xs font-semibold text-slate-700">รหัสบริการ <span class="text-rose-500">*</span></label>
                                <input name="code" value="{{ old('code', $service->code) }}" required class="w-full rounded-xl border-slate-300 text-sm">
                            </div>
                            <div>
                                <label class="mb-1 block text-xs font-semibold text-slate-700">ชื่อหัตถการ (ไทย) <span class="text-rose-500">*</span></label>
                                <input name="name_th" value="{{ old('name_th', $service->name_th) }}" required class="w-full rounded-xl border-slate-300 text-sm">
                            </div>
                            <div>
                                <label class="mb-1 block text-xs font-semibold text-slate-700">ชื่อหัตถการ (อังกฤษ)</label>
                                <input name="name_en" value="{{ old('name_en', $service->name_en) }}" class="w-full rounded-xl border-slate-300 text-sm">
                            </div>
                            <div>
                                <label class="mb-1 block text-xs font-semibold text-slate-700">ราคา (บาท) <span class="text-rose-500">*</span></label>
                                <input name="price" type="number" min="0" step="0.01" value="{{ old('price', $service->price) }}" required class="w-full rounded-xl border-slate-300 text-sm">
                            </div>
                        </div>

                        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                            <div>
                                <label class="mb-1 block text-xs font-semibold text-slate-700">ระยะเวลา (นาที) <span class="text-rose-500">*</span></label>
                                <input name="duration_minutes" type="number" min="5" value="{{ old('duration_minutes', $service->duration_minutes) }}" required class="w-full rounded-xl border-slate-300 text-sm">
                            </div>
                            <div>
                                <label class="mb-1 block text-xs font-semibold text-slate-700">ลำดับแสดงผล</label>
                                <input name="sort_order" type="number" value="{{ old('sort_order', $service->sort_order) }}" class="w-full rounded-xl border-slate-300 text-sm">
                            </div>
                            <div class="sm:col-span-2">
                                <label class="mb-1 block text-xs font-semibold text-slate-700">เปลี่ยนรูปภาพ</label>
                                <input name="image" type="file" accept="image/png,image/jpeg,image/webp" class="w-full rounded-xl border border-slate-300 bg-white p-2 text-xs file:mr-3 file:rounded-md file:border-0 file:bg-brand-50 file:px-3 file:py-1 file:font-medium file:text-brand-700">
                            </div>
                        </div>

                        <div>
                            <label class="mb-1 block text-xs font-semibold text-slate-700">รายละเอียด</label>
                            <textarea name="description" rows="2" class="w-full rounded-xl border-slate-300 text-sm">{{ old('description', $service->description) }}</textarea>
                        </div>

                        <div class="grid gap-4 sm:grid-cols-2">
                            <div>
                                <label class="mb-1.5 block text-xs font-semibold text-slate-700">หมอนวดผู้ให้บริการ</label>
                                <select multiple name="therapist_ids[]" class="min-h-28 w-full rounded-xl border-slate-300 text-sm p-2">
                                    @foreach($therapists as $therapist)
                                        <option value="{{ $therapist->id }}" @selected($service->therapists->contains('id', $therapist->id))>
                                            {{ $therapist->displayName }} ({{ $therapist->code }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="flex flex-col justify-center space-y-3 pt-2">
                                <label class="flex items-center gap-2.5 text-sm font-medium text-slate-800 cursor-pointer">
                                    <input type="checkbox" name="requires_therapist_selection" value="1" @checked($service->requires_therapist_selection) class="h-4 w-4 rounded border-slate-300 text-brand-600 focus:ring-brand-500">
                                    <span>ต้องให้ผู้รับบริการเลือกแพทย์แผนไทย</span>
                                </label>
                                <label class="flex items-center gap-2.5 text-sm font-medium text-slate-800 cursor-pointer">
                                    <input type="checkbox" name="is_active" value="1" @checked($service->is_active) class="h-4 w-4 rounded border-slate-300 text-brand-600 focus:ring-brand-500">
                                    <span>เปิดให้บริการ</span>
                                </label>
                            </div>
                        </div>

                        <div class="flex items-center justify-between pt-2 border-t border-slate-200">
                            <button type="submit" class="rounded-xl bg-brand-700 px-5 py-2 text-sm font-semibold text-white shadow hover:bg-brand-800 transition-colors">
                                บันทึกการแก้ไข
                            </button>
                        </div>
                    </form>

                    <!-- Toggle Status -->
                    <form method="post" action="{{ route('admin.services.status', $service) }}" class="mt-3 text-right">
                        @csrf
                        @method('PATCH')
                        <button type="submit" class="text-xs font-semibold {{ $service->is_active ? 'text-rose-600 hover:text-rose-800' : 'text-emerald-600 hover:text-emerald-800' }} hover:underline">
                            {{ $service->is_active ? 'ปิดการให้บริการหัตถการนี้' : 'เปิดให้บริการหัตถการนี้' }}
                        </button>
                    </form>
                </div>
            </details>
        </div>
    @empty
        <div class="rounded-2xl bg-white p-12 text-center text-slate-400 border border-slate-200 shadow-sm">
            ยังไม่มีรายการหัตถการในระบบ
        </div>
    @endforelse
</div>

@if($services->hasPages())
    <div class="mt-6">
        {{ $services->links() }}
    </div>
@endif
@endsection

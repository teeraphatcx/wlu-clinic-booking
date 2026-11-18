@extends('admin.layout')

@section('title', 'บัญชีและสิทธิ์การใช้งาน')

@section('content')
<div class="mb-6 flex flex-wrap items-end justify-between gap-4">
    <div>
        <p class="mb-1 text-xs font-semibold uppercase tracking-wider text-brand-600">Admin & Roles</p>
        <h1 class="text-2xl font-bold text-slate-800">บัญชีเจ้าหน้าที่และสิทธิ์การใช้งาน</h1>
        <p class="text-sm text-slate-500">จัดการรายชื่อเจ้าหน้าที่ กำหนดระดับสิทธิ์การเข้าถึงเมนู และตรวจสอบประวัติการใช้งานระบบ</p>
    </div>
</div>

{{-- กล่องสรุปสถานะ --}}
<div class="mb-6 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
    <div class="rounded-2xl border border-brand-100 bg-gradient-to-br from-brand-700 to-brand-900 p-5 text-white shadow-sm">
        <p class="text-xs text-brand-100 font-medium">เจ้าหน้าที่ทั้งหมด</p>
        <p class="mt-2 text-3xl font-bold">{{ number_format($admins->total()) }}</p>
        <p class="mt-1 text-xs text-brand-100">ผู้มีสิทธิ์เข้าใช้งานหลังบ้าน</p>
    </div>
    <div class="rounded-2xl border border-emerald-100 bg-white p-5 shadow-sm">
        <p class="text-xs font-semibold text-emerald-600">เปิดใช้งานอยู่</p>
        <p class="mt-2 text-3xl font-bold text-slate-800">{{ number_format($admins->where('is_active', true)->count()) }}</p>
        <p class="mt-1 text-xs text-slate-500">บัญชีที่พร้อมเข้าสู่ระบบ</p>
    </div>
    <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
        <p class="text-xs font-semibold text-slate-500">บทบาทในระบบ</p>
        <p class="mt-2 text-3xl font-bold text-slate-800">{{ $roles->count() }}</p>
        <p class="mt-1 text-xs text-slate-500">ระดับสิทธิ์ (Role Profiles)</p>
    </div>
    <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
        <p class="text-xs font-semibold text-slate-500">บันทึกกิจกรรมล่าสุด</p>
        <p class="mt-2 text-3xl font-bold text-slate-800">{{ number_format($logs->total()) }}</p>
        <p class="mt-1 text-xs text-slate-500">รายการใน Audit Logs</p>
    </div>
</div>

{{-- ฟอร์มเพิ่มเจ้าหน้าที่ใหม่ --}}
<section class="mb-6 rounded-2xl bg-white p-6 shadow-sm border border-slate-100">
    <div class="mb-5 flex items-center justify-between border-b border-slate-100 pb-4">
        <div>
            <h2 class="text-lg font-bold text-slate-800">เพิ่มบัญชีเจ้าหน้าที่ใหม่</h2>
            <p class="mt-0.5 text-xs text-slate-500">กรอกข้อมูลเพื่อสร้างบัญชีผู้ใช้งานสำหรับเจ้าหน้าที่หรือแพทย์แผนไทย</p>
        </div>
        <span class="inline-flex items-center gap-1.5 rounded-full bg-brand-50 px-3 py-1 text-xs font-semibold text-brand-700">
            <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            สร้างบัญชีใหม่
        </span>
    </div>

    <form method="post" action="{{ route('admin.admins.store') }}" class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
        @csrf
        <div>
            <label class="mb-1 block text-xs font-semibold text-slate-700">ชื่อ-นามสกุล <span class="text-rose-500">*</span></label>
            <input name="name" value="{{ old('name') }}" required placeholder="เช่น ธนกฤต มั่งมี" class="w-full rounded-xl border-slate-300 text-sm focus:border-brand-500 focus:ring-brand-200">
        </div>
        <div>
            <label class="mb-1 block text-xs font-semibold text-slate-700">อีเมลสำหรับเข้าสู่ระบบ <span class="text-rose-500">*</span></label>
            <input name="email" type="email" value="{{ old('email') }}" required placeholder="staff@wu.ac.th" class="w-full rounded-xl border-slate-300 text-sm focus:border-brand-500 focus:ring-brand-200">
        </div>
        <div>
            <label class="mb-1 block text-xs font-semibold text-slate-700">บทบาทหน้าที่ (Role) <span class="text-rose-500">*</span></label>
            <select name="role_id" required class="w-full rounded-xl border-slate-300 text-sm focus:border-brand-500 focus:ring-brand-200">
                @foreach($roles as $role)
                    <option value="{{ $role->id }}" @selected(old('role_id') == $role->id)>{{ $role->display_name }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="mb-1 block text-xs font-semibold text-slate-700">รหัสผ่านเริ่มต้น <span class="text-rose-500">*</span></label>
            <input name="password" type="password" required minlength="12" placeholder="อย่างน้อย 12 ตัวอักษร" class="w-full rounded-xl border-slate-300 text-sm focus:border-brand-500 focus:ring-brand-200">
            <p class="mt-1 text-[11px] text-slate-400">ความยาวอย่างน้อย 12 ตัวอักษร</p>
        </div>

        <div class="flex items-center gap-2 sm:col-span-2 lg:col-span-3">
            <label class="relative inline-flex cursor-pointer items-center gap-2">
                <input type="checkbox" name="is_active" value="1" checked class="h-4 w-4 rounded border-slate-300 text-brand-600 focus:ring-brand-500">
                <span class="text-sm font-medium text-slate-700">เปิดใช้งานทันทีหลังจากสร้าง</span>
            </label>
        </div>

        <div class="sm:col-span-2 lg:col-span-1 lg:text-right">
            <button type="submit" class="inline-flex w-full items-center justify-center gap-2 rounded-xl bg-brand-700 px-5 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-brand-800 transition">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/></svg>
                บันทึกบัญชีใหม่
            </button>
        </div>
    </form>
</section>

{{-- ตารางรายชื่อเจ้าหน้าที่ --}}
<section class="mb-8 overflow-hidden rounded-2xl bg-white shadow-sm border border-slate-100">
    <div class="flex flex-wrap items-center justify-between gap-3 border-b border-slate-100 px-6 py-4">
        <div>
            <h2 class="text-lg font-bold text-slate-800">รายชื่อเจ้าหน้าที่ผู้ดูแลระบบ</h2>
            <p class="text-xs text-slate-500">มีทั้งหมด {{ number_format($admins->total()) }} บัญชี</p>
        </div>
        <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-600">สิทธิ์และการจัดการ</span>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full min-w-[760px] text-left text-sm">
            <thead class="bg-slate-50 text-xs font-semibold text-slate-600">
                <tr>
                    <th class="px-6 py-3.5">เจ้าหน้าที่</th>
                    <th class="px-4 py-3.5">บทบาท (Role)</th>
                    <th class="px-4 py-3.5">เข้าใช้งานล่าสุด</th>
                    <th class="px-4 py-3.5">สถานะ</th>
                    <th class="px-6 py-3.5 text-right">การจัดการ</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @foreach($admins as $admin)
                    <tr class="hover:bg-slate-50/70 transition">
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <span class="grid h-10 w-10 shrink-0 place-items-center rounded-xl bg-brand-100 font-bold text-brand-800 shadow-sm text-sm">
                                    {{ mb_substr($admin->name, 0, 1) }}
                                </span>
                                <div class="min-w-0">
                                    <div class="flex items-center gap-2">
                                        <p class="font-semibold text-slate-800">{{ $admin->name }}</p>
                                        @if($admin->id === auth('admin')->id())
                                            <span class="rounded-full bg-brand-50 px-2 py-0.5 text-[10px] font-semibold text-brand-700 border border-brand-100">บัญชีคุณ</span>
                                        @endif
                                    </div>
                                    <p class="text-xs text-slate-500">{{ $admin->email }}</p>
                                </div>
                            </div>

                            {{-- ส่วนแก้ไขข้อมูลเจ้าหน้าที่แบบ Accordion --}}
                            <details class="mt-2.5 rounded-xl border border-slate-200 bg-slate-50/50 p-3">
                                <summary class="cursor-pointer text-xs font-medium text-brand-700 hover:text-brand-900 inline-flex items-center gap-1">
                                    <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                                    แก้ไขข้อมูลบัญชีนี้
                                </summary>
                                <form method="post" action="{{ route('admin.admins.update', $admin) }}" class="mt-3 grid gap-3 sm:grid-cols-2 pt-2 border-t border-slate-200">
                                    @csrf
                                    @method('PUT')
                                    <div>
                                        <label class="mb-1 block text-[11px] font-semibold text-slate-600">ชื่อเจ้าหน้าที่</label>
                                        <input name="name" value="{{ $admin->name }}" required class="w-full rounded-lg border-slate-300 text-xs">
                                    </div>
                                    <div>
                                        <label class="mb-1 block text-[11px] font-semibold text-slate-600">อีเมล</label>
                                        <input name="email" type="email" value="{{ $admin->email }}" required class="w-full rounded-lg border-slate-300 text-xs">
                                    </div>
                                    <div>
                                        <label class="mb-1 block text-[11px] font-semibold text-slate-600">บทบาท</label>
                                        <select name="role_id" class="w-full rounded-lg border-slate-300 text-xs">
                                            @foreach($roles as $role)
                                                <option value="{{ $role->id }}" @selected($admin->role_id === $role->id)>{{ $role->display_name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div>
                                        <label class="mb-1 block text-[11px] font-semibold text-slate-600">รหัสผ่านใหม่ (เว้นว่างหากไม่ต้องการเปลี่ยน)</label>
                                        <input name="password" type="password" minlength="12" placeholder="เว้นว่างหากไม่เปลี่ยน" class="w-full rounded-lg border-slate-300 text-xs">
                                    </div>
                                    <div class="sm:col-span-2 flex items-center justify-between pt-1">
                                        <label class="inline-flex items-center gap-2 text-xs text-slate-700">
                                            <input name="is_active" type="checkbox" value="1" @checked($admin->is_active) class="h-3.5 w-3.5 rounded border-slate-300 text-brand-600">
                                            เปิดใช้งานบัญชี
                                        </label>
                                        <button type="submit" class="rounded-lg bg-brand-700 px-3.5 py-1.5 text-xs font-semibold text-white hover:bg-brand-800 transition">
                                            บันทึกการแก้ไข
                                        </button>
                                    </div>
                                </form>
                            </details>
                        </td>

                        <td class="px-4 py-4">
                            <span class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-semibold {{ $admin->role?->name === 'super_admin' ? 'bg-brand-50 text-brand-700 border border-brand-200' : 'bg-slate-100 text-slate-700' }}">
                                {{ $admin->role?->display_name ?? 'ไม่มีบทบาท' }}
                            </span>
                        </td>

                        <td class="px-4 py-4 text-xs text-slate-600 whitespace-nowrap">
                            @if($admin->last_login_at)
                                <span class="font-medium text-slate-800">{{ $admin->last_login_at->format('d/m/Y') }}</span>
                                <span class="text-slate-400 block text-[11px]">{{ $admin->last_login_at->format('H:i น.') }}</span>
                            @else
                                <span class="text-slate-400">— ยังไม่เคยเข้าสู่ระบบ</span>
                            @endif
                        </td>

                        <td class="px-4 py-4">
                            @if($admin->is_active)
                                <span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-50 px-2.5 py-1 text-xs font-semibold text-emerald-700 border border-emerald-100">
                                    <span class="h-1.5 w-1.5 rounded-full bg-emerald-500"></span>
                                    ใช้งานปกติ
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1.5 rounded-full bg-rose-50 px-2.5 py-1 text-xs font-semibold text-rose-700 border border-rose-100">
                                    <span class="h-1.5 w-1.5 rounded-full bg-rose-500"></span>
                                    ระงับการใช้งาน
                                </span>
                            @endif
                        </td>

                        <td class="px-6 py-4 text-right">
                            @if($admin->id !== auth('admin')->id())
                                <form method="post" action="{{ route('admin.admins.status', $admin) }}" onsubmit="return confirm('ยืนยัน{{ $admin->is_active ? 'ระงับการใช้งาน' : 'เปิดใช้งาน' }} บัญชี {{ $admin->name }} หรือไม่?')">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="inline-flex items-center gap-1 rounded-lg border px-3 py-1.5 text-xs font-semibold transition {{ $admin->is_active ? 'border-rose-200 text-rose-700 bg-rose-50/50 hover:bg-rose-100' : 'border-emerald-200 text-emerald-700 bg-emerald-50/50 hover:bg-emerald-100' }}">
                                        @if($admin->is_active)
                                            <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/></svg>
                                            ระงับบัญชี
                                        @else
                                            <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                            เปิดใช้งาน
                                        @endif
                                    </button>
                                </form>
                            @else
                                <span class="text-xs text-slate-400">บัญชีปัจจุบัน</span>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    @if($admins->hasPages())
        <div class="border-t border-slate-100 px-6 py-4">{{ $admins->links() }}</div>
    @endif
</section>

{{-- ส่วนกำหนดสิทธิ์ตามบทบาท (Role Permissions) --}}
@php
    $groupLabels = [
        'bookings' => ['label' => 'การจองคิว', 'icon' => 'calendar'],
        'customers' => ['label' => 'ข้อมูลผู้รับบริการ', 'icon' => 'users'],
        'services' => ['label' => 'หัตถการ', 'icon' => 'sparkles'],
        'therapists' => ['label' => 'หมอนวด/แพทย์แผนไทย', 'icon' => 'user-check'],
        'schedules' => ['label' => 'ตารางเวลาและวันหยุด', 'icon' => 'clock'],
        'admins' => ['label' => 'ผู้ดูแลระบบและเจ้าหน้าที่', 'icon' => 'shield'],
        'settings' => ['label' => 'การตั้งค่าระบบ', 'icon' => 'cog'],
        'contents' => ['label' => 'ข่าวและเนื้อหา', 'icon' => 'document'],
        'reports' => ['label' => 'รายงานและสถิติ', 'icon' => 'chart'],
        'audit_logs' => ['label' => 'ประวัติการใช้งาน (Audit Logs)', 'icon' => 'shield'],
    ];
    $groupedPermissions = $permissions->groupBy('group');
@endphp

<section class="mb-8">
    <div class="mb-4">
        <h2 class="text-lg font-bold text-slate-800">กำหนดสิทธิ์ตามบทบาท (Role Permissions)</h2>
        <p class="text-xs text-slate-500">เลือกกำหนดสิทธิ์ที่อนุญาตให้แต่ละบทบาทเข้าถึงหรือจัดการในระบบได้</p>
    </div>

    <div class="space-y-4">
        @foreach($roles as $role)
            <div class="overflow-hidden rounded-2xl bg-white shadow-sm border border-slate-100">
                <div class="flex flex-wrap items-center justify-between gap-3 bg-slate-50/70 px-6 py-4 border-b border-slate-100">
                    <div class="flex items-center gap-3">
                        <span class="grid h-9 w-9 place-items-center rounded-xl bg-brand-100 text-brand-800 font-bold text-sm">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                        </span>
                        <div>
                            <div class="flex items-center gap-2">
                                <h3 class="font-bold text-slate-800">{{ $role->display_name }}</h3>
                                <span class="rounded bg-slate-200/60 px-2 py-0.5 text-[10px] font-mono text-slate-600">{{ $role->name }}</span>
                            </div>
                            <p class="text-xs text-slate-500">{{ $role->description }}</p>
                        </div>
                    </div>
                    <span class="rounded-full bg-brand-50 px-3 py-1 text-xs font-semibold text-brand-700 border border-brand-100">
                        มีสิทธิ์ {{ $role->permissions->count() }} จาก {{ $permissions->count() }} รายการ
                    </span>
                </div>

                <div class="p-6">
                    @if($role->name === 'super_admin')
                        <div class="flex items-center gap-3 rounded-xl border border-brand-200 bg-brand-50/60 p-4 text-sm text-brand-900">
                            <svg class="h-5 w-5 shrink-0 text-brand-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            <div>
                                <p class="font-semibold">Super Admin มีสิทธิ์ทั้งหมดในระบบ</p>
                                <p class="text-xs text-brand-700">บทบาทผู้ดูแลระบบสูงสุดได้รับการคุ้มครองสิทธิ์เต็มรูปแบบทุกโมดูลโดยอัตโนมัติ เพื่อป้องกันการล็อกระบบ</p>
                            </div>
                        </div>
                    @else
                        <form method="post" action="{{ route('admin.roles.permissions', $role) }}" id="role-form-{{ $role->id }}">
                            @csrf
                            @method('PUT')

                            <div class="mb-4 flex items-center justify-between border-b border-slate-100 pb-3">
                                <span class="text-xs font-medium text-slate-500">ติ๊กเลือกสิทธิ์ที่ต้องการมอบให้บทบาทนี้</span>
                                <div class="flex gap-2">
                                    <button type="button" onclick="document.querySelectorAll('#role-form-{{ $role->id }} input[type=checkbox]').forEach(el => el.checked = true)" class="rounded-lg border border-slate-200 bg-white px-2.5 py-1 text-xs font-semibold text-slate-700 hover:bg-slate-50">
                                        เลือกทั้งหมด
                                    </button>
                                    <button type="button" onclick="document.querySelectorAll('#role-form-{{ $role->id }} input[type=checkbox]').forEach(el => el.checked = false)" class="rounded-lg border border-slate-200 bg-white px-2.5 py-1 text-xs font-semibold text-slate-700 hover:bg-slate-50">
                                        ยกเลิกทั้งหมด
                                    </button>
                                </div>
                            </div>

                            <div class="space-y-5">
                                @foreach($groupedPermissions as $group => $items)
                                    <div>
                                        <p class="mb-2.5 text-xs font-bold uppercase tracking-wider text-slate-500 flex items-center gap-1.5">
                                            <span class="h-1.5 w-1.5 rounded-full bg-brand-600"></span>
                                            {{ $groupLabels[$group]['label'] ?? ucfirst($group) }}
                                        </p>
                                        <div class="grid gap-2.5 sm:grid-cols-2 lg:grid-cols-3">
                                            @foreach($items as $permission)
                                                <label class="flex items-start gap-3 rounded-xl border border-slate-200 bg-slate-50/50 p-3 hover:bg-slate-50 hover:border-brand-200 cursor-pointer transition">
                                                    <input type="checkbox" name="permission_ids[]" value="{{ $permission->id }}" @checked($role->permissions->contains('id', $permission->id)) class="mt-0.5 h-4 w-4 rounded border-slate-300 text-brand-600 focus:ring-brand-500">
                                                    <div class="min-w-0">
                                                        <span class="block text-xs font-semibold text-slate-800">{{ $permission->display_name }}</span>
                                                        <span class="block font-mono text-[10px] text-slate-400">{{ $permission->name }}</span>
                                                    </div>
                                                </label>
                                            @endforeach
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            <div class="mt-6 border-t border-slate-100 pt-4 flex justify-end">
                                <button type="submit" class="inline-flex items-center gap-2 rounded-xl bg-brand-700 px-5 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-brand-800 transition">
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                    บันทึกสิทธิ์ของบทบาท {{ $role->display_name }}
                                </button>
                            </div>
                        </form>
                    @endif
                </div>
            </div>
        @endforeach
    </div>
</section>

{{-- ประวัติการใช้งานของเจ้าหน้าที่ (Audit Logs) --}}
<section class="overflow-hidden rounded-2xl bg-white shadow-sm border border-slate-100">
    <div class="flex flex-wrap items-center justify-between gap-3 border-b border-slate-100 px-6 py-4">
        <div>
            <h2 class="text-lg font-bold text-slate-800">ประวัติการใช้งานของเจ้าหน้าที่ (Audit Logs)</h2>
            <p class="text-xs text-slate-500">เก็บบันทึกกิจกรรมการสร้าง ปรับปรุง แก้ไข และการเข้าสู่ระบบทั้งหมดเพื่อความโปร่งใสและปลอดภัย</p>
        </div>
        <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-600">บันทึกอัตโนมัติ</span>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full min-w-[760px] text-left text-xs">
            <thead class="bg-slate-50 font-semibold text-slate-600">
                <tr>
                    <th class="px-6 py-3.5">วันและเวลา</th>
                    <th class="px-4 py-3.5">ผู้ปฏิบัติงาน</th>
                    <th class="px-4 py-3.5">กิจกรรม (Action)</th>
                    <th class="px-4 py-3.5">ข้อมูลเป้าหมาย</th>
                    <th class="px-6 py-3.5 text-right">IP Address</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($logs as $log)
                    <tr class="hover:bg-slate-50/70 transition">
                        <td class="px-6 py-3.5 whitespace-nowrap text-slate-700 font-medium">
                            {{ $log->created_at->format('d/m/Y H:i:s') }}
                        </td>
                        <td class="px-4 py-3.5">
                            <div class="flex items-center gap-2">
                                <span class="grid h-6 w-6 place-items-center rounded-lg bg-brand-50 text-[11px] font-bold text-brand-700">
                                    {{ mb_substr($log->admin?->name ?? 'S', 0, 1) }}
                                </span>
                                <span class="font-semibold text-slate-800">{{ $log->admin?->name ?? 'ระบบอัตโนมัติ' }}</span>
                            </div>
                        </td>
                        <td class="px-4 py-3.5">
                            <span class="inline-flex items-center rounded-md bg-slate-100 px-2 py-0.5 font-mono text-[11px] font-medium text-slate-700">
                                {{ $log->action }}
                            </span>
                        </td>
                        <td class="px-4 py-3.5 text-slate-600">
                            @if($log->model_type)
                                <span class="font-medium text-slate-800">{{ class_basename($log->model_type) }}</span>
                                <span class="text-slate-400">#{{ $log->model_id }}</span>
                            @else
                                <span class="text-slate-400">—</span>
                            @endif
                        </td>
                        <td class="px-6 py-3.5 text-right font-mono text-slate-500">
                            {{ $log->ip_address ?? '—' }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-6 py-12 text-center text-sm text-slate-500">
                            ไม่มีบันทึกประวัติการใช้งาน
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($logs->hasPages())
        <div class="border-t border-slate-100 px-6 py-4">{{ $logs->links() }}</div>
    @endif
</section>
@endsection

@extends('admin.layout')

@section('title', 'จัดการผู้ใช้')

@section('content')
<div class="mb-6 flex flex-wrap items-end justify-between gap-4">
    <div>
        <p class="mb-1 text-xs font-semibold uppercase tracking-[0.18em] text-brand-600">User management</p>
        <h1 class="mb-1">จัดการผู้ใช้</h1>
        <p class="text-sm text-slate-500">รวมผู้รับบริการและเจ้าหน้าที่ไว้ในรายการเดียว ค้นหาและตรวจสอบสถานะได้จากหน้านี้</p>
    </div>
    <div class="flex flex-wrap gap-2">
        @if($canViewCustomers)
            <a href="{{ route('admin.customers.index') }}" class="rounded-xl border border-brand-200 bg-white px-4 py-2 text-sm font-semibold text-brand-700 hover:bg-brand-50">ข้อมูลผู้รับบริการ</a>
        @endif
        @if($canViewAdmins)
            <a href="{{ route('admin.admins.index') }}" class="rounded-xl bg-brand-700 px-4 py-2 text-sm font-semibold text-white hover:bg-brand-800">บัญชีและสิทธิ์</a>
        @endif
    </div>
</div>

<div class="mb-6 grid gap-3 sm:grid-cols-2 xl:grid-cols-4">
    <div class="rounded-2xl border border-brand-100 bg-gradient-to-br from-brand-700 to-brand-900 p-5 text-white shadow-sm">
        <p class="text-xs text-brand-100">ผู้ใช้ที่มองเห็นได้</p>
        <p class="mt-2 text-3xl font-bold">{{ number_format($stats['total']) }}</p>
        <p class="mt-1 text-xs text-brand-100">จากทุกประเภทบัญชี</p>
    </div>
    <div class="rounded-2xl border border-emerald-100 bg-emerald-50 p-5 shadow-sm">
        <p class="text-xs font-semibold text-emerald-700">กำลังใช้งาน</p>
        <p class="mt-2 text-3xl font-bold text-emerald-900">{{ number_format($stats['active']) }}</p>
        <p class="mt-1 text-xs text-emerald-700">บัญชีที่ยังเปิดใช้งาน</p>
    </div>
    @if($canViewCustomers)
        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <p class="text-xs font-semibold text-slate-500">ผู้รับบริการ</p>
            <p class="mt-2 text-3xl font-bold text-slate-800">{{ number_format($stats['customers']) }}</p>
            <p class="mt-1 text-xs text-slate-500">ข้อมูลที่ใช้กับการจองคิว</p>
        </div>
    @endif
    @if($canViewAdmins)
        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <p class="text-xs font-semibold text-slate-500">เจ้าหน้าที่</p>
            <p class="mt-2 text-3xl font-bold text-slate-800">{{ number_format($stats['admins']) }}</p>
            <p class="mt-1 text-xs text-slate-500">บัญชีสำหรับเข้าใช้งานหลังบ้าน</p>
        </div>
    @endif
</div>

<section class="mb-6 rounded-2xl bg-white p-5 shadow-sm">
    <form method="get" action="{{ route('admin.users.index') }}" class="grid gap-3 md:grid-cols-[minmax(0,1fr)_180px_160px_auto_auto] md:items-end">
        <label class="block">
            <span class="mb-1 block text-xs font-semibold text-slate-500">ค้นหาผู้ใช้</span>
            <input name="search" value="{{ request('search') }}" placeholder="ชื่อ อีเมล เบอร์โทร หรือ HN" class="w-full rounded-xl border-slate-300 text-sm">
        </label>
        <label class="block">
            <span class="mb-1 block text-xs font-semibold text-slate-500">ประเภทผู้ใช้</span>
            <select name="type" class="w-full rounded-xl border-slate-300 text-sm">
                <option value="all">ทุกประเภท</option>
                @if($canViewCustomers)<option value="customer" @selected(request('type') === 'customer')>ผู้รับบริการ</option>@endif
                @if($canViewAdmins)<option value="admin" @selected(request('type') === 'admin')>เจ้าหน้าที่</option>@endif
            </select>
        </label>
        <label class="block">
            <span class="mb-1 block text-xs font-semibold text-slate-500">สถานะ</span>
            <select name="state" class="w-full rounded-xl border-slate-300 text-sm">
                <option value="">ทุกสถานะ</option>
                <option value="1" @selected(request('state') === '1')>ใช้งาน</option>
                <option value="0" @selected(request('state') === '0')>ระงับ</option>
            </select>
        </label>
        <button class="rounded-xl bg-brand-700 px-4 py-2.5 text-sm font-semibold text-white hover:bg-brand-800">ค้นหา</button>
        <a href="{{ route('admin.users.index') }}" class="rounded-xl border border-slate-200 px-4 py-2.5 text-center text-sm font-semibold text-slate-600 hover:bg-slate-50">ล้างตัวกรอง</a>
    </form>
</section>

<section class="overflow-hidden rounded-2xl bg-white shadow-sm">
    <div class="flex flex-wrap items-center justify-between gap-3 border-b border-slate-100 px-5 py-4">
        <div>
            <h2>รายชื่อผู้ใช้ทั้งหมด</h2>
            <p class="mt-1 text-xs text-slate-500">{{ number_format($users->total()) }} รายการที่ตรงกับตัวกรอง</p>
        </div>
        <span class="rounded-full bg-brand-50 px-3 py-1 text-xs font-semibold text-brand-700">อัปเดตตามข้อมูลล่าสุด</span>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full min-w-[900px] text-left text-sm">
            <thead class="bg-slate-50 text-slate-600">
                <tr>
                    <th class="px-5 py-3">ผู้ใช้</th>
                    <th>ประเภท</th>
                    <th>ข้อมูลอ้างอิง</th>
                    <th>กิจกรรม</th>
                    <th>สถานะ</th>
                    <th>สร้างเมื่อ</th>
                    <th class="px-5"></th>
                </tr>
            </thead>
            <tbody>
                @forelse($users as $user)
                    <tr class="border-t border-slate-100 align-top">
                        <td class="px-5 py-4">
                            <div class="flex items-center gap-3">
                                <span class="grid h-10 w-10 shrink-0 place-items-center rounded-xl {{ $user['type'] === 'admin' ? 'bg-brand-100 text-brand-700' : 'bg-amber-50 text-amber-700' }} font-bold">{{ mb_substr($user['name'], 0, 1) }}</span>
                                <div class="min-w-0">
                                    <p class="font-semibold text-slate-800">{{ $user['name'] }}</p>
                                    <p class="mt-1 truncate text-xs text-slate-500">{{ $user['email'] ?: ($user['phone'] ?: 'ไม่มีข้อมูลติดต่อ') }}</p>
                                </div>
                            </div>
                        </td>
                        <td>
                            <span class="rounded-full px-2.5 py-1 text-xs font-semibold {{ $user['type'] === 'admin' ? 'bg-brand-50 text-brand-700' : 'bg-amber-50 text-amber-700' }}">{{ $user['type_label'] }}</span>
                        </td>
                        <td class="text-xs text-slate-600">{{ $user['reference'] }}</td>
                        <td class="text-xs text-slate-600">{{ $user['activity'] }}</td>
                        <td>
                            <span class="rounded-full px-2.5 py-1 text-xs font-semibold {{ $user['is_active'] ? 'bg-emerald-50 text-emerald-700' : 'bg-rose-50 text-rose-700' }}">{{ $user['is_active'] ? 'ใช้งาน' : 'ระงับ' }}</span>
                        </td>
                        <td class="whitespace-nowrap text-xs text-slate-500">{{ $user['created_at']?->format('d/m/Y') ?? '—' }}</td>
                        <td class="px-5">
                            <div class="flex flex-wrap items-center justify-end gap-2 py-1">
                                @if($user['type'] === 'customer')
                                    <a href="{{ route('admin.customers.show', $user['id']) }}" class="text-xs font-semibold text-brand-700 hover:underline">ประวัติ</a>
                                    @if($canManageCustomers)
                                        <a href="{{ route('admin.customers.index', ['search' => $user['phone']]) }}" class="text-xs font-semibold text-slate-600 hover:underline">แก้ไข</a>
                                        <form method="post" action="{{ route('admin.customers.status', $user['id']) }}">
                                            @csrf
                                            @method('PATCH')
                                            <button class="text-xs font-semibold {{ $user['is_active'] ? 'text-rose-700' : 'text-emerald-700' }}">{{ $user['is_active'] ? 'ระงับ' : 'เปิดใช้' }}</button>
                                        </form>
                                    @endif
                                @elseif($canViewAdmins)
                                    @if($canManageAdmins)
                                        <a href="{{ route('admin.admins.index') }}" class="text-xs font-semibold text-brand-700 hover:underline">แก้ไข</a>
                                        @if($user['id'] !== auth('admin')->id())
                                            <form method="post" action="{{ route('admin.admins.status', $user['id']) }}">
                                                @csrf
                                                @method('PATCH')
                                                <button class="text-xs font-semibold {{ $user['is_active'] ? 'text-rose-700' : 'text-emerald-700' }}">{{ $user['is_active'] ? 'ระงับ' : 'เปิดใช้' }}</button>
                                            </form>
                                        @endif
                                    @endif
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-5 py-12 text-center text-sm text-slate-500">ไม่พบผู้ใช้ตามเงื่อนไขที่เลือก</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($users->hasPages())
        <div class="border-t border-slate-100 px-5 py-4">{{ $users->links() }}</div>
    @endif
</section>
@endsection

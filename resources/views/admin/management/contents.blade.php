@extends('admin.layout')
@section('title', 'ข่าวและเนื้อหาเว็บไซต์')

@section('content')
<div class="mb-6 flex flex-col justify-between gap-4 sm:flex-row sm:items-center">
    <div>
        <p class="text-xs font-semibold uppercase tracking-wider text-brand-600">Content Management</p>
        <h1 class="text-2xl font-bold text-slate-900 mt-0.5">ข่าว ประชาสัมพันธ์ แบนเนอร์ และ FAQ</h1>
        <p class="mt-1 text-sm text-slate-500">จัดการเนื้อหาและข่าวสารที่ปรากฏบนหน้าเว็บไซต์หลักของคลินิก</p>
    </div>
</div>

<!-- Add Content Accordion -->
<details class="group mb-6 rounded-2xl bg-white border border-slate-200 shadow-sm overflow-hidden" {{ $errors->any() ? 'open' : '' }}>
    <summary class="flex cursor-pointer items-center justify-between p-5 font-semibold text-slate-800 hover:bg-slate-50 transition-colors select-none">
        <div class="flex items-center gap-3">
            <span class="grid h-8 w-8 place-items-center rounded-xl bg-brand-50 text-brand-700 font-bold text-lg">＋</span>
            <div>
                <p class="font-bold text-slate-900 text-base">เพิ่มเนื้อหาใหม่</p>
                <p class="text-xs text-slate-500 font-normal">เขียนข่าว ประกาศสำคัญ ภาพแบนเนอร์ หรือคำถามที่พบบ่อย</p>
            </div>
        </div>
        <span class="text-xs text-brand-700 font-semibold group-open:rotate-180 transition-transform duration-200">▼</span>
    </summary>

    <div class="border-t border-slate-100 p-5 sm:p-6 bg-slate-50/50">
        <form method="post" action="{{ route('admin.contents.store') }}" enctype="multipart/form-data" class="space-y-4">
            @csrf
            <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                <div>
                    <label class="mb-1 block text-xs font-semibold text-slate-700">ประเภทเนื้อหา <span class="text-rose-500">*</span></label>
                    <select name="type" class="w-full rounded-xl border-slate-300 text-sm focus:border-brand-500 focus:ring-brand-200">
                        <option value="news">ข่าวประชาสัมพันธ์</option>
                        <option value="announcement">ประกาศ</option>
                        <option value="banner">แบนเนอร์</option>
                        <option value="faq">คำถามที่พบบ่อย (FAQ)</option>
                    </select>
                </div>
                <div class="lg:col-span-2">
                    <label class="mb-1 block text-xs font-semibold text-slate-700">หัวข้อ <span class="text-rose-500">*</span></label>
                    <input name="title" value="{{ old('title') }}" required placeholder="หัวข้อข่าวหรือประกาศ" class="w-full rounded-xl border-slate-300 text-sm focus:border-brand-500 focus:ring-brand-200">
                </div>
                <div>
                    <label class="mb-1 block text-xs font-semibold text-slate-700">ลำดับการแสดงผล</label>
                    <input name="sort_order" type="number" value="{{ old('sort_order', 0) }}" class="w-full rounded-xl border-slate-300 text-sm focus:border-brand-500 focus:ring-brand-200">
                </div>
            </div>

            <div>
                <label class="mb-1 block text-xs font-semibold text-slate-700">ข้อความย่อ (Excerpt)</label>
                <input name="excerpt" value="{{ old('excerpt') }}" placeholder="สรุปสั้น 1-2 บรรทัด สำหรับแสดงในการ์ดตัวอย่าง" class="w-full rounded-xl border-slate-300 text-sm focus:border-brand-500 focus:ring-brand-200">
            </div>

            <div>
                <label class="mb-1 block text-xs font-semibold text-slate-700">เนื้อหาฉบับเต็ม <span class="text-rose-500">*</span></label>
                <textarea name="content" required rows="4" placeholder="เนื้อหารายละเอียดทั้งหมด..." class="w-full rounded-xl border-slate-300 text-sm focus:border-brand-500 focus:ring-brand-200">{{ old('content') }}</textarea>
            </div>

            <div class="grid gap-4 sm:grid-cols-3 items-end">
                <div>
                    <label class="mb-1 block text-xs font-semibold text-slate-700">รูปภาพหน้าปก / แบนเนอร์</label>
                    <input name="image" type="file" accept="image/png,image/jpeg,image/webp" class="w-full rounded-xl border border-slate-300 bg-white p-2 text-xs file:mr-3 file:rounded-md file:border-0 file:bg-brand-50 file:px-3 file:py-1 file:font-medium file:text-brand-700">
                </div>
                <div>
                    <label class="mb-1 block text-xs font-semibold text-slate-700">วันที่เริ่มเผยแพร่</label>
                    <input name="published_at" type="datetime-local" class="w-full rounded-xl border-slate-300 text-sm focus:border-brand-500 focus:ring-brand-200">
                </div>
                <div>
                    <label class="mb-1 block text-xs font-semibold text-slate-700">วันที่หมดอายุ / สิ้นสุด</label>
                    <input name="expired_at" type="datetime-local" class="w-full rounded-xl border-slate-300 text-sm focus:border-brand-500 focus:ring-brand-200">
                </div>
            </div>

            <div class="flex items-center justify-between pt-2 border-t border-slate-100">
                <label class="flex items-center gap-2.5 text-sm font-medium text-slate-800 cursor-pointer">
                    <input type="checkbox" name="is_active" value="1" checked class="h-4 w-4 rounded border-slate-300 text-brand-600 focus:ring-brand-500">
                    <span>เผยแพร่ทันทีบนเว็บไซต์</span>
                </label>
                <button type="submit" class="rounded-xl bg-brand-700 px-6 py-2.5 text-sm font-semibold text-white shadow-md hover:bg-brand-800 transition-colors">
                    บันทึกเนื้อหา
                </button>
            </div>
        </form>
    </div>
</details>

<!-- Content Cards -->
<div class="space-y-4">
    @forelse($contents as $content)
        <div class="rounded-2xl bg-white border border-slate-200 shadow-sm overflow-hidden">
            <details class="group">
                <summary class="flex cursor-pointer items-center justify-between p-5 hover:bg-slate-50 transition-colors select-none">
                    <div class="flex items-center gap-4">
                        @if($content->image)
                            <img src="{{ asset('storage/'.$content->image) }}" alt="{{ $content->title }}" class="h-14 w-14 rounded-xl object-cover border border-slate-200 shrink-0">
                        @else
                            <div class="grid h-14 w-14 shrink-0 place-items-center rounded-xl bg-brand-50 text-brand-700 font-bold text-lg">
                                📰
                            </div>
                        @endif
                        <div>
                            <div class="flex items-center gap-2">
                                <h3 class="font-bold text-slate-900 text-base">{{ $content->title }}</h3>
                                <span class="rounded px-2 py-0.5 text-xs font-medium bg-slate-100 text-slate-600">
                                    {{ ['news' => 'ข่าวประชาสัมพันธ์', 'announcement' => 'ประกาศ', 'banner' => 'แบนเนอร์', 'faq' => 'FAQ'][$content->type] ?? $content->type }}
                                </span>
                            </div>
                            <p class="text-xs text-slate-500 mt-1 line-clamp-1 max-w-xl">
                                {{ $content->excerpt ?: Str::limit($content->content, 120) }}
                            </p>
                        </div>
                    </div>
                    <div class="flex items-center gap-3">
                        <span class="rounded-full px-3 py-1 text-xs font-semibold {{ $content->is_active ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-slate-100 text-slate-500 border border-slate-200' }}">
                            {{ $content->is_active ? 'เผยแพร่อยู่' : 'ซ่อน' }}
                        </span>
                        <span class="text-xs text-brand-700 font-semibold group-open:rotate-180 transition-transform duration-200">
                            แก้ไข ▼
                        </span>
                    </div>
                </summary>

                <!-- Edit Form -->
                <div class="border-t border-slate-100 p-5 sm:p-6 bg-slate-50/50">
                    <form method="post" action="{{ route('admin.contents.update', $content) }}" enctype="multipart/form-data" class="space-y-4">
                        @csrf
                        @method('PUT')
                        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                            <div>
                                <label class="mb-1 block text-xs font-semibold text-slate-700">ประเภท</label>
                                <select name="type" class="w-full rounded-xl border-slate-300 text-sm">
                                    <option value="news" @selected($content->type === 'news')>ข่าวประชาสัมพันธ์</option>
                                    <option value="announcement" @selected($content->type === 'announcement')>ประกาศ</option>
                                    <option value="banner" @selected($content->type === 'banner')>แบนเนอร์</option>
                                    <option value="faq" @selected($content->type === 'faq')>FAQ</option>
                                </select>
                            </div>
                            <div class="lg:col-span-2">
                                <label class="mb-1 block text-xs font-semibold text-slate-700">หัวข้อ</label>
                                <input name="title" value="{{ old('title', $content->title) }}" required class="w-full rounded-xl border-slate-300 text-sm">
                            </div>
                            <div>
                                <label class="mb-1 block text-xs font-semibold text-slate-700">ลำดับ</label>
                                <input name="sort_order" type="number" value="{{ old('sort_order', $content->sort_order) }}" class="w-full rounded-xl border-slate-300 text-sm">
                            </div>
                        </div>

                        <div>
                            <label class="mb-1 block text-xs font-semibold text-slate-700">ข้อความย่อ</label>
                            <input name="excerpt" value="{{ old('excerpt', $content->excerpt) }}" class="w-full rounded-xl border-slate-300 text-sm">
                        </div>

                        <div>
                            <label class="mb-1 block text-xs font-semibold text-slate-700">เนื้อหาฉบับเต็ม</label>
                            <textarea name="content" required rows="4" class="w-full rounded-xl border-slate-300 text-sm">{{ old('content', $content->content) }}</textarea>
                        </div>

                        <div class="grid gap-4 sm:grid-cols-3 items-end">
                            <div>
                                <label class="mb-1 block text-xs font-semibold text-slate-700">เปลี่ยนรูปภาพ</label>
                                <input name="image" type="file" accept="image/png,image/jpeg,image/webp" class="w-full rounded-xl border border-slate-300 bg-white p-2 text-xs file:mr-3 file:rounded-md file:border-0 file:bg-brand-50 file:px-3 file:py-1 file:font-medium file:text-brand-700">
                            </div>
                            <div>
                                <label class="mb-1 block text-xs font-semibold text-slate-700">เผยแพร่เมื่อ</label>
                                <input name="published_at" type="datetime-local" value="{{ $content->published_at?->format('Y-m-d\TH:i') }}" class="w-full rounded-xl border-slate-300 text-sm">
                            </div>
                            <div>
                                <label class="mb-1 block text-xs font-semibold text-slate-700">หมดอายุเมื่อ</label>
                                <input name="expired_at" type="datetime-local" value="{{ $content->expired_at?->format('Y-m-d\TH:i') }}" class="w-full rounded-xl border-slate-300 text-sm">
                            </div>
                        </div>

                        <div class="flex items-center justify-between pt-2 border-t border-slate-200">
                            <label class="flex items-center gap-2.5 text-sm font-medium text-slate-800 cursor-pointer">
                                <input type="checkbox" name="is_active" value="1" @checked($content->is_active) class="h-4 w-4 rounded border-slate-300 text-brand-600 focus:ring-brand-500">
                                <span>เปิดเผยแพร่บนเว็บไซต์</span>
                            </label>
                            <button type="submit" class="rounded-xl bg-brand-700 px-6 py-2 text-sm font-semibold text-white shadow hover:bg-brand-800 transition-colors">
                                บันทึกการแก้ไข
                            </button>
                        </div>
                    </form>

                    <!-- Toggle Status -->
                    <form method="post" action="{{ route('admin.contents.status', $content) }}" class="mt-3 text-right">
                        @csrf
                        @method('PATCH')
                        <button type="submit" class="text-xs font-semibold {{ $content->is_active ? 'text-rose-600 hover:text-rose-800' : 'text-emerald-600 hover:text-emerald-800' }} hover:underline">
                            {{ $content->is_active ? 'ซ่อนเนื้อหานี้' : 'เปิดเผยแพร่เนื้อหานี้' }}
                        </button>
                    </form>
                </div>
            </details>
        </div>
    @empty
        <div class="rounded-2xl bg-white p-12 text-center text-slate-400 border border-slate-200 shadow-sm">
            ยังไม่มีเนื้อหา ข่าวสาร หรือประกาศในระบบ
        </div>
    @endforelse
</div>

@if($contents->hasPages())
    <div class="mt-6">
        {{ $contents->links() }}
    </div>
@endif
@endsection

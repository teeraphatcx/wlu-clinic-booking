@extends('layouts.app')

@section('title', 'บริการหัตถการ')

@section('content')
<section class="bg-gradient-to-br from-brand-900 via-brand-800 to-clinic-900 text-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 sm:py-16">
        <p class="text-sm font-semibold text-brand-200">คลินิกแพทย์แผนไทย</p>
        <h1 class="mt-2 text-3xl sm:text-4xl font-bold font-heading">บริการหัตถการ</h1>
        <p class="mt-3 max-w-2xl text-brand-100">เลือกบริการดูแลสุขภาพที่เหมาะกับท่าน ดูรายละเอียดและอัตราค่าบริการก่อนจองคิว</p>
    </div>
</section>

<section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 sm:py-14">
    @if($services->isEmpty())
        <div class="rounded-2xl border border-slate-200 bg-white p-8 text-center shadow-sm">
            <h2 class="text-lg font-semibold text-slate-800">ขณะนี้ยังไม่มีบริการที่เปิดให้บริการ</h2>
            <p class="mt-2 text-sm text-slate-500">กรุณากลับมาตรวจสอบอีกครั้ง หรือติดต่อคลินิกเพื่อสอบถามข้อมูล</p>
        </div>
    @else
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5 lg:gap-7">
            @foreach($services as $service)
                <article class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm transition hover:-translate-y-1 hover:shadow-lg">
                    @if($service->image)
                        <img src="{{ asset('storage/'.$service->image) }}" alt="{{ $service->name_th }}" width="640" height="192" loading="lazy" decoding="async" class="h-48 w-full object-cover">
                    @else
                        <div class="flex h-40 items-center justify-center bg-gradient-to-br from-brand-100 to-clinic-100 text-brand-600">
                            <svg class="h-12 w-12" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6v12m6-6H6m14 0a8 8 0 11-16 0 8 8 0 0116 0z"/></svg>
                        </div>
                    @endif
                    <div class="p-5 sm:p-6">
                        <div class="flex flex-wrap items-center gap-2 text-xs">
                            <span class="rounded-full bg-brand-50 px-3 py-1 font-medium text-brand-700">{{ $service->duration_minutes }} นาที</span>
                            <span class="rounded-full bg-slate-100 px-3 py-1 text-slate-600">{{ $service->requires_therapist_selection ? 'เลือกแพทย์ได้' : 'จัดแพทย์ให้อัตโนมัติ' }}</span>
                        </div>
                        <h2 class="mt-4 text-lg font-bold text-slate-900">{{ $service->name_th }}</h2>
                        @if($service->name_en)<p class="mt-1 text-xs text-slate-400">{{ $service->name_en }}</p>@endif
                        <p class="mt-3 min-h-12 text-sm leading-6 text-slate-600">{{ \Illuminate\Support\Str::limit($service->description, 130) }}</p>
                        <div class="mt-5 flex items-end justify-between gap-3 border-t border-slate-100 pt-4">
                            <div><p class="text-xs text-slate-500">อัตราค่าบริการ</p><p class="text-xl font-bold text-brand-700">฿{{ number_format((float) $service->price, 0) }}</p></div>
                            <a href="{{ route('services.show', $service) }}" class="rounded-xl border border-brand-200 px-4 py-2.5 text-sm font-semibold text-brand-700 transition hover:bg-brand-50">ดูรายละเอียด</a>
                        </div>
                    </div>
                </article>
            @endforeach
        </div>
    @endif
</section>
@endsection

@extends('layouts.app')

@section('title', $service->name_th)

@section('content')
<section class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-8 sm:py-12">
    <a href="{{ route('services.index') }}" class="inline-flex items-center gap-2 text-sm font-medium text-brand-700 hover:text-brand-900">
        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
        กลับไปหน้าบริการทั้งหมด
    </a>

    <article class="mt-5 overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm">
        @if($service->image)
            <img src="{{ asset('storage/'.$service->image) }}" alt="{{ $service->name_th }}" width="1024" height="448" fetchpriority="high" decoding="async" class="max-h-[28rem] w-full object-cover">
        @else
            <div class="flex h-48 sm:h-64 items-center justify-center bg-gradient-to-br from-brand-100 to-clinic-100 text-brand-600">
                <svg class="h-16 w-16" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6v12m6-6H6m14 0a8 8 0 11-16 0 8 8 0 0116 0z"/></svg>
            </div>
        @endif
        <div class="p-6 sm:p-10">
            <div class="flex flex-wrap gap-2 text-xs">
                <span class="rounded-full bg-brand-50 px-3 py-1.5 font-medium text-brand-700">ใช้เวลาประมาณ {{ $service->duration_minutes }} นาที</span>
                <span class="rounded-full bg-slate-100 px-3 py-1.5 text-slate-600">{{ $service->requires_therapist_selection ? 'เลือกแพทย์ผู้ให้บริการได้' : 'ระบบจัดแพทย์ที่ว่างให้โดยอัตโนมัติ' }}</span>
            </div>
            <h1 class="mt-5 text-2xl sm:text-3xl font-bold font-heading text-slate-900">{{ $service->name_th }}</h1>
            @if($service->name_en)<p class="mt-1 text-sm text-slate-400">{{ $service->name_en }}</p>@endif
            <div class="prose prose-slate mt-6 max-w-none whitespace-pre-line text-sm leading-7 text-slate-600">{{ $service->description ?: 'สอบถามรายละเอียดเพิ่มเติมได้ที่คลินิกแพทย์แผนไทย' }}</div>

            @if($service->therapists->isNotEmpty())
                <div class="mt-8 border-t border-slate-100 pt-6">
                    <h2 class="text-lg font-semibold text-slate-900">แพทย์แผนไทยที่ให้บริการ</h2>
                    <div class="mt-4 grid gap-3 sm:grid-cols-2">
                        @foreach($service->therapists as $therapist)
                            <div class="flex items-center gap-3 rounded-2xl border border-brand-100 bg-brand-50/60 p-3">
                                @if($therapist->photo)
                                    <img src="{{ asset('storage/'.$therapist->photo) }}" alt="{{ $therapist->displayName }}" width="56" height="56" loading="lazy" decoding="async" class="h-14 w-14 shrink-0 rounded-xl object-cover border border-white shadow-sm">
                                @else
                                    <div class="grid h-14 w-14 shrink-0 place-items-center rounded-xl bg-brand-700 text-lg font-bold text-white">
                                        {{ mb_substr($therapist->first_name, 0, 1) }}
                                    </div>
                                @endif
                                <div class="min-w-0">
                                    <p class="font-semibold text-brand-900">{{ $therapist->displayName }}</p>
                                    @if($therapist->bio)
                                        <p class="mt-1 text-xs leading-5 text-slate-600">{{ \Illuminate\Support\Str::limit($therapist->bio, 90) }}</p>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            <div class="mt-8 flex flex-col gap-4 border-t border-slate-100 pt-6 sm:flex-row sm:items-center sm:justify-between">
                <div><p class="text-sm text-slate-500">อัตราค่าบริการ</p><p class="text-2xl font-bold text-brand-700">฿{{ number_format((float) $service->price, 0) }}</p></div>
                <a href="{{ route('booking.select-date', $service) }}" class="inline-flex items-center justify-center gap-2 rounded-xl bg-brand-600 px-6 py-3 font-semibold text-white shadow-sm transition hover:bg-brand-700">
                    จองบริการนี้
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7m7-7H3"/></svg>
                </a>
            </div>
        </div>
    </article>
</section>
@endsection

<!DOCTYPE html>
<html lang="th">
@php($siteLogo = \App\Models\Setting::assetPath('site_logo'))
@php($siteHeader = \App\Models\Setting::assetPath('site_header_image'))
@php($siteHeaderLink = \App\Models\Setting::get('site_header_link'))
@php($clinicName = \App\Models\Setting::get('clinic_name_th', 'คลินิกแพทย์แผนไทย'))
@php($hospitalName = \App\Models\Setting::get('hospital_name', 'โรงพยาบาลศูนย์การแพทย์ มหาวิทยาลัยวลัยลักษณ์'))
@php($clinicPhone = \App\Models\Setting::get('clinic_phone', '0-7567-3000'))
@php($clinicAddress = \App\Models\Setting::get('clinic_address', '222 ต.ไทยบุรี อ.ท่าศาลา จ.นครศรีธรรมราช 80160'))
@php($clinicDescription = \App\Models\Setting::get('clinic_description', ''))
@php($clinicOpenHours = \App\Models\ClinicSchedule::openHoursSummary())
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'ระบบจองคิวออนไลน์') - คลินิกแพทย์แผนไทย ศูนย์การแพทย์ ม.วลัยลักษณ์</title>
    @if($siteLogo)<link rel="icon" href="{{ asset('storage/'.$siteLogo) }}">
    @endif

    <!-- Google Fonts: Noto Sans Thai & Prompt -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+Thai:wght@300;400;500;600;700&family=Prompt:wght@400;500;600;700&display=swap" rel="stylesheet">

    @vite('resources/css/app.css')

    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <style>
        [x-cloak] { display: none !important; }
        body { font-family: 'Noto Sans Thai', sans-serif; }
        h1, h2, h3, h4, h5, h6, .font-heading { font-family: 'Prompt', 'Noto Sans Thai', sans-serif; }
    </style>
    @stack('styles')
</head>
<body class="bg-slate-50 text-slate-800 min-h-screen flex flex-col antialiased selection:bg-brand-500 selection:text-white">

    <!-- Main Navigation Bar -->
    <nav class="bg-white border-b border-slate-200 sticky top-0 z-40 shadow-sm" x-data="{ mobileMenuOpen: false }">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-20">
                
                <!-- Logo & Brand Name -->
                <a href="{{ route('home') }}" class="flex items-center gap-3.5 group">
                    @if($siteLogo)
                        <img src="{{ asset('storage/'.$siteLogo) }}" alt="โลโก้คลินิก" width="64" height="64" decoding="async" class="w-16 h-16 rounded-2xl object-contain bg-white p-1 shadow-md shadow-brand-500/20 group-hover:scale-105 transition-transform duration-200">
                    @else
                        <div class="w-16 h-16 rounded-2xl bg-gradient-to-br from-brand-500 to-clinic-700 flex items-center justify-center text-white shadow-md shadow-brand-500/20 group-hover:scale-105 transition-transform duration-200">
                            <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                            </svg>
                        </div>
                    @endif
                    <div>
                        <div class="font-heading font-bold text-lg sm:text-xl text-slate-900 tracking-tight leading-tight group-hover:text-brand-600 transition-colors">
                            {{ $clinicName }}
                        </div>
                        <div class="text-xs text-slate-500 leading-none mt-1">
                            {{ $hospitalName }}
                        </div>
                    </div>
                </a>

                <!-- Desktop Nav Links -->
                <div class="hidden md:flex items-center gap-1 lg:gap-2">
                    <a href="{{ route('home') }}" class="px-3.5 py-2 rounded-lg text-sm font-medium transition-colors {{ request()->routeIs('home') ? 'bg-brand-50 text-brand-600 font-semibold' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100' }}">
                        หน้าแรก
                    </a>
                    <a href="{{ route('services.index') }}" class="px-3.5 py-2 rounded-lg text-sm font-medium transition-colors {{ request()->routeIs('services.*') ? 'bg-brand-50 text-brand-600 font-semibold' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100' }}">
                        บริการหัตถการ
                    </a>
                    <a href="{{ route('booking.check-form') }}" class="px-3.5 py-2 rounded-lg text-sm font-medium transition-colors {{ request()->routeIs('booking.check*') || request()->routeIs('booking.show') ? 'bg-brand-50 text-brand-600 font-semibold' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100' }}">
                        ตรวจสอบคิวของฉัน
                    </a>
                    <a href="{{ route('contact') }}" class="px-3.5 py-2 rounded-lg text-sm font-medium transition-colors {{ request()->routeIs('contact') ? 'bg-brand-50 text-brand-600 font-semibold' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100' }}">
                        ติดต่อเรา
                    </a>

                    <!-- Booking CTA Button -->
                    <div class="ml-3 pl-3 border-l border-slate-200">
                        <a href="{{ route('booking.index') }}" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl font-heading font-semibold text-sm text-white bg-gradient-to-r from-brand-600 to-brand-500 hover:from-brand-500 hover:to-brand-600 shadow-md shadow-brand-500/25 hover:shadow-lg transition-all duration-200 hover:-translate-y-0.5">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                            <span>จองคิวออนไลน์</span>
                        </a>
                    </div>
                    <a href="{{ route('admin.login') }}" class="ml-2 inline-flex items-center px-4 py-2.5 rounded-xl border border-brand-200 text-brand-700 hover:bg-brand-50 font-semibold text-sm transition-colors">
                        เข้าสู่ระบบ
                    </a>
                </div>

                <!-- Mobile Menu Hamburger -->
                <div class="md:hidden flex items-center">
                    <button @click="mobileMenuOpen = !mobileMenuOpen" type="button" class="p-2 rounded-lg text-slate-600 hover:text-slate-900 hover:bg-slate-100 focus:outline-none">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" x-show="!mobileMenuOpen">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                        </svg>
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" x-show="mobileMenuOpen" x-cloak>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>

            </div>
        </div>

        <!-- Mobile Menu Drawer -->
        <div class="md:hidden border-t border-slate-200 bg-white px-4 pt-3 pb-5 space-y-2" x-show="mobileMenuOpen" x-cloak x-transition>
            <a href="{{ route('home') }}" class="block px-3 py-2.5 rounded-lg text-base font-medium {{ request()->routeIs('home') ? 'bg-brand-50 text-brand-600 font-semibold' : 'text-slate-700' }}">
                หน้าแรก
            </a>
            <a href="{{ route('services.index') }}" class="block px-3 py-2.5 rounded-lg text-base font-medium {{ request()->routeIs('services.*') ? 'bg-brand-50 text-brand-600 font-semibold' : 'text-slate-700' }}">
                บริการหัตถการ
            </a>
            <a href="{{ route('booking.check-form') }}" class="block px-3 py-2.5 rounded-lg text-base font-medium {{ request()->routeIs('booking.check*') ? 'bg-brand-50 text-brand-600 font-semibold' : 'text-slate-700' }}">
                ตรวจสอบคิวของฉัน
            </a>
            <a href="{{ route('contact') }}" class="block px-3 py-2.5 rounded-lg text-base font-medium {{ request()->routeIs('contact') ? 'bg-brand-50 text-brand-600 font-semibold' : 'text-slate-700' }}">
                ติดต่อเรา
            </a>
            <div class="pt-2">
                <a href="{{ route('booking.index') }}" class="flex items-center justify-center gap-2 w-full py-3 rounded-xl font-heading font-semibold text-white bg-brand-600 shadow-md">
                    <span>จองคิวออนไลน์</span>
                </a>
                <a href="{{ route('admin.login') }}" class="flex items-center justify-center w-full py-3 mt-2 rounded-xl font-semibold text-brand-700 border border-brand-200 bg-brand-50">
                    เข้าสู่ระบบเจ้าหน้าที่
                </a>
            </div>
        </div>
    </nav>

    @if($siteHeader)
        <div class="w-full bg-brand-100">
            @if($siteHeaderLink)
                <a href="{{ $siteHeaderLink }}" class="block transition-opacity hover:opacity-90" @if(str_starts_with($siteHeaderLink, 'http')) target="_blank" rel="noopener noreferrer" @endif>
                    <img src="{{ asset('storage/'.$siteHeader) }}" alt="ภาพ Header เว็บไซต์" fetchpriority="high" decoding="async" class="max-h-72 w-full object-cover">
                </a>
            @else
                <img src="{{ asset('storage/'.$siteHeader) }}" alt="ภาพ Header เว็บไซต์" fetchpriority="high" decoding="async" class="max-h-72 w-full object-cover">
            @endif
        </div>
    @endif

    <!-- Global Flash Notification Messages -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 w-full mt-4">
        @if(session('success'))
            <div class="mb-4 p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 flex items-start gap-3 shadow-sm">
                <svg class="w-5 h-5 text-emerald-600 mt-0.5 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                </svg>
                <div class="flex-1 font-medium text-sm">
                    {{ session('success') }}
                </div>
            </div>
        @endif

        @if($errors->any())
            <div class="mb-4 p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 flex items-start gap-3 shadow-sm">
                <svg class="w-5 h-5 text-rose-600 mt-0.5 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                </svg>
                <div class="flex-1 text-sm">
                    <div class="font-semibold mb-1">เกิดข้อผิดพลาด:</div>
                    <ul class="list-disc list-inside space-y-0.5 text-xs sm:text-sm">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        @endif
    </div>

    <!-- Main View Content Area -->
    <main class="flex-1">
        @yield('content')
    </main>

    <!-- Clinic Information Strip -->
    <section class="mt-16 bg-clinic-900 px-4 py-4 text-sm text-brand-100">
        <div class="mx-auto flex max-w-7xl flex-col items-center justify-between gap-3 sm:flex-row">
            <div class="flex items-center gap-2 text-center sm:text-left">
                <span class="inline-block h-2.5 w-2.5 shrink-0 rounded-full bg-emerald-400 animate-pulse"></span>
                <span>{{ $hospitalName }} • {{ $clinicDescription ?: 'ให้บริการตรวจรักษาและฟื้นฟูสุขภาพด้วยศาสตร์แพทย์แผนไทย' }}</span>
            </div>
            <div class="flex items-center gap-3 whitespace-nowrap text-xs sm:text-sm">
                <span><i class="opacity-75">โทร:</i> <strong>{{ $clinicPhone }}</strong></span>
                <span class="hidden md:inline">•</span>
                <span class="hidden md:inline">{{ $clinicOpenHours }}</span>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-slate-900 text-slate-400 border-t border-slate-800">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                
                <!-- Col 1: About Clinic -->
                <div class="md:col-span-2 space-y-3">
                    <div class="flex items-center gap-3">
                        @if($siteLogo)
                            <img src="{{ asset('storage/'.$siteLogo) }}" alt="โลโก้คลินิก" width="40" height="40" loading="lazy" decoding="async" class="h-10 w-10 rounded-lg bg-white object-contain p-0.5">
                        @else
                            <div class="w-9 h-9 rounded-lg bg-brand-600 flex items-center justify-center text-white font-bold">มวล.</div>
                        @endif
                        <span class="font-heading font-bold text-lg text-white">{{ $clinicName }}</span>
                    </div>
                    <p class="text-sm text-slate-400 leading-relaxed max-w-md">
                        {{ $hospitalName }} {{ $clinicDescription ?: 'มุ่งมั่นให้บริการด้านการตรวจ วินิจฉัย บำบัดรักษา และฟื้นฟูสุขภาพด้วยศาสตร์การแพทย์แผนไทยและแพทย์แผนไทยประยุกต์มาตรฐานระดับสากล' }}
                    </p>
                    <div class="text-xs text-slate-500 pt-2">
                        &copy; {{ date('Y') }} {{ $hospitalName }}. สงวนลิขสิทธิ์ทั้งหมด.
                    </div>
                </div>

                <!-- Col 2: Quick Links -->
                <div>
                    <h3 class="font-heading font-semibold text-white text-sm tracking-wider uppercase mb-4">บริการออนไลน์</h3>
                    <ul class="space-y-2 text-sm">
                        <li><a href="{{ route('booking.index') }}" class="hover:text-brand-400 transition-colors">จองคิวออนไลน์</a></li>
                        <li><a href="{{ route('booking.check-form') }}" class="hover:text-brand-400 transition-colors">ตรวจสอบหรือเลื่อนนัด</a></li>
                        <li><a href="{{ route('services.index') }}" class="hover:text-brand-400 transition-colors">อัตราค่าบริการหัตถการ</a></li>
                        <li><a href="{{ route('privacy') }}" class="hover:text-brand-400 transition-colors">นโยบายความเป็นส่วนตัว (PDPA)</a></li>
                    </ul>
                </div>

                <!-- Col 3: Contact Details -->
                <div>
                    <h3 class="font-heading font-semibold text-white text-sm tracking-wider uppercase mb-4">ติดต่อคลินิก</h3>
                    <ul class="space-y-2 text-sm">
                        <li class="flex items-start gap-2">
                            <svg class="w-4 h-4 text-brand-500 mt-1 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                            <span>{{ $clinicAddress }}</span>
                        </li>
                        <li class="flex items-center gap-2">
                            <svg class="w-4 h-4 text-brand-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path></svg>
                            <span>{{ $clinicPhone }}</span>
                        </li>
                        <li class="flex items-center gap-2">
                            <svg class="w-4 h-4 text-brand-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            <span>{{ $clinicOpenHours }}</span>
                        </li>
                    </ul>
                </div>

            </div>
        </div>
    </footer>

    @stack('scripts')
</body>
</html>

<!doctype html>
<html lang="th">
<head>
    @php
        $siteLogo = \App\Models\Setting::assetPath('site_logo');
        $adminClinicName = \App\Models\Setting::get('clinic_name_th', 'คลินิกแพทย์แผนไทย');
    @endphp
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @if($siteLogo)<link rel="icon" href="{{ asset('storage/'.$siteLogo) }}">@endif
    @vite('resources/css/app.css')
    <title>@yield('title','หลังบ้าน') - {{ $adminClinicName }}</title>
</head>
<body class="admin-body min-h-screen bg-gradient-to-br from-brand-50/70 via-white to-clinic-50/60 text-slate-800 antialiased selection:bg-brand-200 selection:text-brand-900">
    @php
        $adminUser = auth('admin')->user();
        $menuGroups = [
            ['label' => 'ภาพรวม', 'items' => [
                ['แดชบอร์ด', 'admin.dashboard', 'reports.view', 'admin.dashboard', 'home'],
            ]],
            ['label' => 'งานบริการ', 'items' => [
                ['จัดการคิว', 'admin.bookings.index', 'bookings.view', 'admin.bookings.*', 'calendar'],
                ['หัตถการ', 'admin.services.index', 'services.view', 'admin.services.*', 'sparkles'],
                ['หมอนวดและตาราง', 'admin.therapists.index', 'therapists.view', 'admin.therapists.*', 'user-check'],
                ['วันเวลาและรอบคิว', 'admin.schedules.index', 'schedules.view', 'admin.schedules.*', 'clock'],
            ]],
            ['label' => 'บริหารระบบ', 'items' => [
                ['จัดการผู้ใช้', 'admin.users.index', 'customers.view|admins.view', 'admin.users.*', 'users'],
                ['ข่าวและเนื้อหา', 'admin.contents.index', 'contents.view', 'admin.contents.*', 'document'],
                ['รายงาน', 'admin.reports.index', 'reports.view', 'admin.reports.*', 'chart'],
                ['บัญชีและสิทธิ์', 'admin.admins.index', 'admins.view', 'admin.admins.*', 'shield'],
                ['ตั้งค่าระบบ', 'admin.settings.edit', 'settings.view', 'admin.settings.*', 'cog'],
            ]],
        ];
    @endphp

    <div id="adminSidebarOverlay" class="admin-sidebar-overlay" aria-hidden="true"></div>
    <aside id="adminSidebar" class="admin-sidebar" aria-label="เมนูระบบจัดการ">
        <a href="{{ route('admin.dashboard') }}" class="admin-brand">
            @if($siteLogo)
                <img src="{{ asset('storage/'.$siteLogo) }}" alt="โลโก้คลินิก" class="admin-brand-logo">
            @else
                <span class="admin-brand-fallback">มวล.</span>
            @endif
            <span class="min-w-0"><strong>{{ $adminClinicName }}</strong><small>ระบบจัดการคลินิก</small></span>
        </a>

        <div class="mb-4 flex items-center justify-between rounded-2xl border border-white/10 bg-white/5 px-3 py-2.5">
            <div class="flex items-center gap-2">
                <span class="relative flex h-2.5 w-2.5">
                    <span class="absolute inline-flex h-full w-full animate-ping rounded-full bg-emerald-300 opacity-60"></span>
                    <span class="relative inline-flex h-2.5 w-2.5 rounded-full bg-emerald-400"></span>
                </span>
                <span class="text-[11px] font-medium text-white/80">ระบบพร้อมใช้งาน</span>
            </div>
            <span class="rounded-full bg-emerald-400/10 px-2 py-0.5 text-[10px] font-bold uppercase tracking-[0.16em] text-emerald-200">Live</span>
        </div>

        <div class="admin-sidebar-label">เมนูหลัก</div>
        <nav class="admin-nav">
            @foreach($menuGroups as $group)
                <div class="admin-nav-group">
                    <p class="admin-nav-heading">{{ $group['label'] }}</p>
                    @foreach($group['items'] as [$label, $routeName, $permission, $activePattern, $icon])
                        @if(collect(explode('|', $permission))->contains(fn ($permissionName) => $adminUser?->hasPermission($permissionName)))
                            <a href="{{ route($routeName) }}" @if(request()->routeIs($activePattern)) aria-current="page" @endif class="admin-nav-link {{ request()->routeIs($activePattern) ? 'is-active' : '' }}">
                                <span class="admin-nav-icon" aria-hidden="true">
                                    @switch($icon)
                                        @case('home')
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                                            @break
                                        @case('calendar')
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                            @break
                                        @case('sparkles')
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"/></svg>
                                            @break
                                        @case('user-check')
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                            @break
                                        @case('clock')
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                            @break
                                        @case('users')
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                                            @break
                                        @case('document')
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"/></svg>
                                            @break
                                        @case('chart')
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                                            @break
                                        @case('shield')
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                                            @break
                                        @case('cog')
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                            @break
                                        @default
                                            <span>•</span>
                                    @endswitch
                                </span>
                                <span>{{ $label }}</span>
                            </a>
                        @endif
                    @endforeach
                </div>
            @endforeach
        </nav>

        <div class="admin-sidebar-bottom">
            <a href="{{ route('home') }}" target="_blank" class="admin-nav-link">
                <span class="admin-nav-icon" aria-hidden="true">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                </span>
                <span>ดูหน้าเว็บไซต์</span>
            </a>
            <div class="admin-user-card">
                <span class="admin-user-avatar" aria-hidden="true">{{ mb_substr($adminUser?->name ?? 'A', 0, 1) }}</span>
                <span class="min-w-0 flex-1"><strong>{{ $adminUser?->name }}</strong><small>เจ้าหน้าที่ระบบ</small></span>
                <form method="post" action="{{ route('admin.logout') }}">
                    @csrf
                    <button type="submit" class="admin-logout" aria-label="ออกจากระบบ" title="ออกจากระบบ">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                    </button>
                </form>
            </div>
        </div>
    </aside>

    <div class="admin-main">
        <header class="admin-topbar">
            <button id="adminMenuToggle" type="button" class="admin-menu-toggle" aria-label="เปิดเมนู" aria-expanded="false" aria-controls="adminSidebar">
                <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M4 6h16M4 12h16M4 18h16"/></svg>
            </button>
            <div class="min-w-0">
                <span class="admin-topbar-eyebrow">{{ $adminClinicName }}</span>
                <strong>@yield('title', 'ระบบจัดการ')</strong>
            </div>
            <div class="hidden md:flex items-center gap-2 rounded-full border border-brand-100 bg-brand-50/70 px-3 py-1.5 text-xs font-medium text-brand-700">
                <svg class="w-3.5 h-3.5 mr-1 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                <span>{{ \Carbon\Carbon::now()->locale('th')->isoFormat('D MMMM YYYY') }}</span>
            </div>
            <div class="admin-topbar-user rounded-full border border-brand-100 bg-brand-50/70 py-1.5 pl-2 pr-3">
                <span class="admin-status-dot"></span>
                <span class="grid h-7 w-7 place-items-center rounded-full bg-brand-700 text-[11px] font-bold text-white">{{ mb_substr($adminUser?->name ?? 'A', 0, 1) }}</span>
                <span class="hidden sm:inline">{{ $adminUser?->name }}</span>
            </div>
        </header>

        <main class="admin-content relative overflow-hidden">
            <div class="pointer-events-none absolute inset-x-0 top-0 h-56 bg-gradient-to-b from-brand-50/80 via-brand-50/30 to-transparent"></div>
            <div class="relative z-10">
                @if(session('success'))
                    <div role="status" class="admin-alert admin-alert-success"><span aria-hidden="true">✓</span>{{ session('success') }}</div>
                @endif
                @if(session('error'))
                    <div role="alert" class="admin-alert admin-alert-error"><span aria-hidden="true">!</span>{{ session('error') }}</div>
                @endif
                @if($errors->any())
                    <div role="alert" class="admin-alert admin-alert-error"><span aria-hidden="true">!</span>{{ $errors->first() }}</div>
                @endif
                @yield('content')
            </div>
        </main>
        <footer class="admin-footer">© {{ date('Y') }} โรงพยาบาลศูนย์การแพทย์ มหาวิทยาลัยวลัยลักษณ์</footer>
    </div>

    <script>
        (() => {
            const sidebar = document.getElementById('adminSidebar');
            const overlay = document.getElementById('adminSidebarOverlay');
            const toggle = document.getElementById('adminMenuToggle');
            const closeMenu = () => {
                sidebar.classList.remove('is-open');
                overlay.classList.remove('is-visible');
                toggle.setAttribute('aria-expanded', 'false');
                document.body.classList.remove('admin-menu-open');
            };
            toggle.addEventListener('click', () => {
                const open = sidebar.classList.toggle('is-open');
                overlay.classList.toggle('is-visible', open);
                toggle.setAttribute('aria-expanded', String(open));
                document.body.classList.toggle('admin-menu-open', open);
            });
            overlay.addEventListener('click', closeMenu);
            document.addEventListener('keydown', event => { if (event.key === 'Escape') closeMenu(); });
            sidebar.querySelectorAll('a').forEach(link => link.addEventListener('click', closeMenu));
        })();
    </script>
</body>
</html>

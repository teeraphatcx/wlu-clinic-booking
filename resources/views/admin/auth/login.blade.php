<!doctype html>
<html lang="th">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    @php($siteLogo = \App\Models\Setting::assetPath('site_logo'))
    @if($siteLogo)<link rel="icon" href="{{ asset('storage/'.$siteLogo) }}">@endif
    <title>เข้าสู่ระบบเจ้าหน้าที่ | คลินิกแพทย์แผนไทย ม.วลัยลักษณ์</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+Thai:wght@400;500;600;700&display=swap" rel="stylesheet">

    <script src="https://cdn.tailwindcss.com?plugins=forms"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: { sans: ['"Noto Sans Thai"', 'ui-sans-serif', 'system-ui', 'sans-serif'] },
                    colors: {
                        brand: {
                            50: '#f7f3fb', 100: '#eee7f6', 200: '#ddcfee', 300: '#c5addf',
                            400: '#a988c5', 500: '#8f6ab2', 600: '#7b5aa6', 700: '#644986',
                            800: '#503b6c', 900: '#3d2d52'
                        }
                    },
                    keyframes: {
                        rise: { '0%': { opacity: 0, transform: 'translateY(16px)' }, '100%': { opacity: 1, transform: 'translateY(0)' } },
                        float: { '0%,100%': { transform: 'translateY(0)' }, '50%': { transform: 'translateY(-14px)' } }
                    },
                    animation: {
                        rise: 'rise .6s ease-out both',
                        float: 'float 7s ease-in-out infinite'
                    }
                }
            }
        }
    </script>
</head>

<body class="font-sans min-h-screen relative overflow-hidden bg-gradient-to-br from-brand-900 via-brand-800 to-brand-600 flex items-center justify-center p-4">

    {{-- พื้นหลังตกแต่ง --}}
    <div class="pointer-events-none absolute -top-24 -left-24 h-80 w-80 rounded-full bg-brand-400/30 blur-3xl animate-float"></div>
    <div class="pointer-events-none absolute -bottom-28 -right-20 h-96 w-96 rounded-full bg-brand-300/20 blur-3xl animate-float" style="animation-delay:-3s"></div>
    <div class="pointer-events-none absolute inset-0 opacity-[0.07]"
         style="background-image:radial-gradient(#fff 1px,transparent 1px);background-size:22px 22px"></div>
    <div id="mouseGlow" class="pointer-events-none fixed z-0 hidden h-72 w-72 -translate-x-1/2 -translate-y-1/2 rounded-full bg-brand-300/15 blur-3xl transition-opacity duration-300 lg:block"></div>

    <main id="loginCard" class="relative z-10 w-full max-w-md animate-rise transition-transform duration-150 ease-out">

        <div class="rounded-3xl bg-white/95 backdrop-blur shadow-2xl shadow-brand-900/40 ring-1 ring-white/40 overflow-hidden">

            {{-- แถบสีด้านบน --}}
            <div class="h-1.5 bg-gradient-to-r from-brand-400 via-brand-600 to-brand-800"></div>

            <div class="p-7 sm:p-9">

                {{-- หัวข้อ --}}
                <div class="text-center mb-8">
                    @if($siteLogo)
                        <img src="{{ asset('storage/'.$siteLogo) }}" alt="โลโก้คลินิก" class="mx-auto h-20 w-20 rounded-2xl bg-white object-contain p-2 shadow-lg shadow-brand-600/30 ring-4 ring-brand-100">
                    @else
                        <div class="mx-auto h-16 w-16 rounded-2xl bg-gradient-to-br from-brand-500 to-brand-800 text-white grid place-items-center text-lg font-bold shadow-lg shadow-brand-600/40 ring-4 ring-brand-100">มวล.</div>
                    @endif
                    <h1 class="mt-5 text-2xl font-bold text-slate-900">ระบบเจ้าหน้าที่</h1>
                    <p class="mt-1 text-sm text-slate-500">คลินิกแพทย์แผนไทย มหาวิทยาลัยวลัยลักษณ์</p>
                </div>

                {{-- ข้อความผิดพลาดจาก session --}}
                @if(session('error'))
                    <div role="alert" class="mb-5 flex items-start gap-3 rounded-xl border border-red-200 bg-red-50 p-3.5 text-sm text-red-700">
                        <svg class="mt-0.5 h-5 w-5 shrink-0" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M18 10A8 8 0 11 2 10a8 8 0 0116 0zm-8-4a1 1 0 00-1 1v3a1 1 0 002 0V7a1 1 0 00-1-1zm0 8a1.1 1.1 0 100-2.2A1.1 1.1 0 0010 14z" clip-rule="evenodd"/></svg>
                        <span>{{ session('error') }}</span>
                    </div>
                @endif

                <form method="post" action="{{ route('admin.login.submit') }}" class="space-y-5" id="loginForm">
                    @csrf

                    {{-- อีเมล --}}
                    <div>
                        <label for="email" class="block text-sm font-medium text-slate-700 mb-1.5">อีเมล</label>
                        <div class="relative">
                            <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3.5 text-slate-400">
                                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="5" width="18" height="14" rx="2"/><path d="m3 7 9 6 9-6"/></svg>
                            </span>
                            <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username"
                                   placeholder="name@wu.ac.th"
                                   class="w-full rounded-xl border-slate-200 bg-slate-50 py-3 pl-11 pr-4 text-slate-900 placeholder-slate-400 transition focus:border-brand-500 focus:bg-white focus:ring-4 focus:ring-brand-200/60">
                        </div>
                    </div>

                    {{-- รหัสผ่าน --}}
                    <div>
                        <label for="password" class="block text-sm font-medium text-slate-700 mb-1.5">รหัสผ่าน</label>
                        <div class="relative">
                            <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3.5 text-slate-400">
                                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="4" y="11" width="16" height="10" rx="2"/><path d="M8 11V7a4 4 0 0 1 8 0v4"/></svg>
                            </span>
                            <input id="password" type="password" name="password" required autocomplete="current-password"
                                   placeholder="••••••••"
                                   class="w-full rounded-xl border-slate-200 bg-slate-50 py-3 pl-11 pr-12 text-slate-900 placeholder-slate-400 transition focus:border-brand-500 focus:bg-white focus:ring-4 focus:ring-brand-200/60">
                            <button type="button" id="togglePw" aria-label="แสดง/ซ่อนรหัสผ่าน"
                                    class="absolute inset-y-0 right-0 flex items-center pr-3.5 text-slate-400 hover:text-brand-600 transition">
                                <svg id="eyeOn" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M2 12s3.6-7 10-7 10 7 10 7-3.6 7-10 7S2 12 2 12z"/><circle cx="12" cy="12" r="3"/></svg>
                                <svg id="eyeOff" class="hidden h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M17.9 17.9A10.1 10.1 0 0 1 12 19c-6.4 0-10-7-10-7a18.7 18.7 0 0 1 4.1-5M9.9 5.2A9.8 9.8 0 0 1 12 5c6.4 0 10 7 10 7a18.5 18.5 0 0 1-2.2 3.2M1 1l22 22"/><path d="M14.1 14.1a3 3 0 1 1-4.2-4.2"/></svg>
                            </button>
                        </div>
                    </div>

                    {{-- จดจำการเข้าสู่ระบบ --}}
                    <label class="flex cursor-pointer items-center gap-2.5 text-sm text-slate-600 select-none">
                        <input type="checkbox" name="remember" value="1"
                               class="h-4 w-4 rounded border-slate-300 text-brand-600 focus:ring-brand-500">
                        จดจำการเข้าสู่ระบบ
                    </label>

                    {{-- ข้อผิดพลาดจาก validation --}}
                    @if($errors->any())
                        <div role="alert" class="flex items-start gap-2.5 rounded-xl border border-red-200 bg-red-50 p-3 text-sm text-red-700">
                            <svg class="mt-0.5 h-4 w-4 shrink-0" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M18 10A8 8 0 11 2 10a8 8 0 0116 0zm-8-4a1 1 0 00-1 1v3a1 1 0 002 0V7a1 1 0 00-1-1zm0 8a1.1 1.1 0 100-2.2A1.1 1.1 0 0010 14z" clip-rule="evenodd"/></svg>
                            <p>{{ $errors->first() }}</p>
                        </div>
                    @endif

                    {{-- ปุ่มเข้าสู่ระบบ --}}
                    <button type="submit" id="submitBtn"
                            class="group relative flex w-full items-center justify-center gap-2 rounded-xl bg-gradient-to-r from-brand-600 to-brand-800 py-3.5 font-semibold text-white shadow-lg shadow-brand-700/30 transition hover:from-brand-700 hover:to-brand-900 hover:shadow-xl focus:outline-none focus:ring-4 focus:ring-brand-300 active:scale-[.99] disabled:opacity-70">
                        <svg id="spinner" class="hidden h-5 w-5 animate-spin" viewBox="0 0 24 24" fill="none"><circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" class="opacity-25"/><path d="M4 12a8 8 0 018-8" stroke="currentColor" stroke-width="4" stroke-linecap="round" class="opacity-90"/></svg>
                        <span id="btnText">เข้าสู่ระบบ</span>
                        <svg id="btnArrow" class="h-5 w-5 transition group-hover:translate-x-1" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14M13 6l6 6-6 6"/></svg>
                    </button>
                </form>

                {{-- ลิงก์กลับ --}}
                <div class="mt-7 border-t border-slate-100 pt-5 text-center">
                    <a href="{{ route('home') }}" class="inline-flex items-center gap-1.5 text-sm font-medium text-brand-700 transition hover:text-brand-900">
                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 12H5M11 18l-6-6 6-6"/></svg>
                        กลับหน้าเว็บไซต์
                    </a>
                </div>
            </div>
        </div>

        <p class="mt-6 text-center text-xs text-brand-200/80">
            © {{ date('Y') }} โรงพยาบาลศูนย์การแพทย์ มหาวิทยาลัยวลัยลักษณ์
        </p>
    </main>

    <script>
        // เอฟเฟกต์แสงและการ์ดเคลื่อนตามเมาส์บน Desktop
        const glow = document.getElementById('mouseGlow');
        const card = document.getElementById('loginCard');
        let frame;
        window.addEventListener('pointermove', (event) => {
            if (window.matchMedia('(pointer: coarse)').matches) return;
            cancelAnimationFrame(frame);
            frame = requestAnimationFrame(() => {
                const x = event.clientX;
                const y = event.clientY;
                const centerX = window.innerWidth / 2;
                const centerY = window.innerHeight / 2;
                const rotateY = ((x - centerX) / centerX) * 2.2;
                const rotateX = ((centerY - y) / centerY) * 2.2;
                glow.style.left = `${x}px`;
                glow.style.top = `${y}px`;
                card.style.transform = `perspective(1100px) rotateX(${rotateX}deg) rotateY(${rotateY}deg)`;
            });
        });
        window.addEventListener('pointerleave', () => {
            card.style.transform = '';
        });

        // แสดง/ซ่อนรหัสผ่าน
        const pw = document.getElementById('password');
        document.getElementById('togglePw').addEventListener('click', () => {
            const show = pw.type === 'password';
            pw.type = show ? 'text' : 'password';
            document.getElementById('eyeOn').classList.toggle('hidden', show);
            document.getElementById('eyeOff').classList.toggle('hidden', !show);
        });

        // สถานะกำลังเข้าสู่ระบบ (กันกดซ้ำ)
        document.getElementById('loginForm').addEventListener('submit', () => {
            const btn = document.getElementById('submitBtn');
            btn.disabled = true;
            document.getElementById('spinner').classList.remove('hidden');
            document.getElementById('btnArrow').classList.add('hidden');
            document.getElementById('btnText').textContent = 'กำลังเข้าสู่ระบบ...';
        });
    </script>
</body>
</html>

<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminAuthenticate
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! auth('admin')->check()) {
            return redirect()->route('admin.login')->with('error', 'กรุณาเข้าสู่ระบบเจ้าหน้าที่ก่อน');
        }

        if (! auth('admin')->user()->is_active) {
            auth('admin')->logout();

            return redirect()->route('admin.login')->withErrors(['email' => 'บัญชีนี้ถูกระงับการใช้งาน']);
        }

        return $next($request);
    }
}

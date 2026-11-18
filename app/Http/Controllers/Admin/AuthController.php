<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function showLogin()
    {
        if (Auth::guard('admin')->check()) {
            return redirect()->route('admin.dashboard');
        }

        return view('admin.auth.login');
    }

    public function login(Request $request)
    {
        if (Auth::guard('admin')->check()) {
            return redirect()->route('admin.dashboard');
        }

        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ], [], ['email' => 'อีเมล', 'password' => 'รหัสผ่าน']);

        if (! Auth::guard('admin')->attempt([...$credentials, 'is_active' => true], $request->boolean('remember'))) {
            $admin = Admin::where('email', $credentials['email'])->first();
            if ($admin && ! $admin->is_active) {
                return back()->withErrors(['email' => 'บัญชีผู้ใช้นี้ถูกระงับการใช้งาน'])->withInput();
            }

            return back()->withErrors(['email' => 'อีเมลหรือรหัสผ่านไม่ถูกต้อง'])->withInput();
        }

        $request->session()->regenerate();
        Auth::guard('admin')->user()->update(['last_login_at' => now()]);

        return redirect()->intended(route('admin.dashboard'));
    }

    public function logout(Request $request)
    {
        Auth::guard('admin')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('admin.login');
    }
}

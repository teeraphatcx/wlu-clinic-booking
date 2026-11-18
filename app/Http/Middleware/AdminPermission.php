<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminPermission
{
    public function handle(Request $request, Closure $next, string ...$permissions): Response
    {
        $admin = auth('admin')->user();
        $hasPermission = false;

        foreach ($permissions as $permissionSet) {
            foreach (explode('|', $permissionSet) as $permission) {
                if ($admin?->hasPermission($permission) ?? false) {
                    $hasPermission = true;

                    break 2;
                }
            }
        }

        abort_unless($hasPermission, 403, 'คุณไม่มีสิทธิ์เข้าถึงเมนูนี้');

        return $next($request);
    }
}

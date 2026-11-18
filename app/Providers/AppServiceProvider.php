<?php

namespace App\Providers;

use App\Contracts\NotificationDriver;
use App\Services\Notifications\EmailNotificationDriver;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(NotificationDriver::class, EmailNotificationDriver::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Rate Limiter for Booking submission (5 requests/minute per IP)
        RateLimiter::for('booking', function (Request $request) {
            return Limit::perMinute(5)->by($request->ip());
        });

        // Rate Limiter for Check-Booking search (10 requests/minute per IP)
        RateLimiter::for('check-booking', function (Request $request) {
            return Limit::perMinute(10)->by($request->ip());
        });

        // Rate Limiter for Admin login (5 requests/minute per IP)
        RateLimiter::for('admin-login', function (Request $request) {
            return Limit::perMinute(5)->by($request->ip());
        });
    }
}

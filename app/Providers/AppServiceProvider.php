<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Define custom rate limiters for form operations
        RateLimiter::for('form-submission', function (Request $request) {
            return $request->user()
                ? Limit::perMinute(10)->by($request->user()->id) // 10 per user per minute
                : Limit::perMinute(3)->by($request->ip()); // 3 per IP for guests
        });

        RateLimiter::for('draft-creation', function (Request $request) {
            return $request->user()
                ? Limit::perMinute(20)->by($request->user()->id)
                : Limit::perMinute(5)->by($request->ip());
        });

        RateLimiter::for('answer-save', function (Request $request) {
            // More lenient for saving (user navigating between sections)
            return $request->user()
                ? Limit::perMinute(30)->by($request->user()->id)
                : Limit::perMinute(10)->by($request->ip());
        });
    }
}

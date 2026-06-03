<?php

namespace App\Providers;

use Dedoc\Scramble\Scramble;
use Illuminate\Support\ServiceProvider;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Dedoc\Scramble\Support\Generator\OpenApi;
use Dedoc\Scramble\Support\Generator\SecurityScheme;

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
        // Scramble (Token-based authentication)
        Scramble::afterOpenApiGenerated(function (OpenApi $openApi) {
            $openApi->secure(
                SecurityScheme::http('bearer')
            );
        });

        RateLimiter::for('login_register', function (Request $request) {
            return Limit::perMinute(10)->by($request->ip())->response(function () {
                return errorResponse('Too many login or register attempts. Please try again later.', [], 429);
            });
        });

        RateLimiter::for('verify_otp', function (Request $request) {
            return Limit::perMinute(5)->by($request->ip())->response(function () {
                return errorResponse('Too many verification attempts. Please wait a minute.', [], 429);
            });
        });

        RateLimiter::for('forgot_password', function (Request $request) {
            $phone = $request->input('phone') ?? $request->ip();

            return Limit::perHour(3)->by($phone)->response(function () {
                return errorResponse('Daily or hourly OTP limit reached for this phone number. Try again later.', [], 429);
            });
        });
    }
}

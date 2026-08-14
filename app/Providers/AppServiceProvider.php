<?php

namespace App\Providers;

use App\Models\Booth;
use App\Models\Event;
use App\Observers\BoothObserver;
use App\Observers\EventObserver;
use Dedoc\Scramble\Scramble;
use Dedoc\Scramble\Support\Generator\OpenApi;
use Dedoc\Scramble\Support\Generator\SecurityScheme;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

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

        if (! app()->isLocal()) {
            URL::forceScheme('https');
        }

        RateLimiter::for('login_register', function (Request $request) {
            return Limit::perMinute(10)->by($request->ip())->response(function () {
                return errorResponse(__('rate_limit.login_register'), [], 429);
            });
        });

        RateLimiter::for('verify_otp', function (Request $request) {
            return Limit::perMinute(5)->by($request->ip())->response(function () {
                return errorResponse(__('rate_limit.verify_otp'), [], 429);
            });
        });

        RateLimiter::for('forgot_password', function (Request $request) {
            $phone = $request->input('phone') ?? $request->ip();

            return Limit::perMinute(10)->by($phone)->response(function () { // perHour to perMinute for testing
                return errorResponse(__('rate_limit.forgot_password'), [], 429);
            });
        });

        RateLimiter::for('profile_update', function (Request $request) { // 2 to 20 for testing
            return Limit::perMinute(20)->by($request->user()->id)->response(function () {
                return errorResponse(__('rate_limit.profile_update'), [], 429);
            });
        });

        RateLimiter::for('password_update', function (Request $request) {
            return Limit::perMinute(10)->by($request->user()->id)->response(function () { // 3 to 10 for testing
                return errorResponse(__('rate_limit.password_update'), [], 429);
            });
        });

        RateLimiter::for('phone_update_request', function (Request $request) {
            return Limit::perHour(2)->by($request->user()->id)->response(function () {
                return errorResponse(__('rate_limit.phone_update_request'), [], 429);
            });
        });

        RateLimiter::for('report', function (Request $reqeust) {
            return Limit::perHour(5)->by($reqeust->user()->id)->response(function () {
                return errorResponse(__('rate_limit.report'), [], 429);
            });
        });
    }
}

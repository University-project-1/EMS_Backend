<?php

namespace App\Providers;

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
        Scramble::afterOpenApiGenerated(function (OpenApi $openApi) {
            $openApi->secure(SecurityScheme::http('bearer'));
        });

        if (! app()->isLocal()) {
            URL::forceScheme('https');
        }

        $this->registerRateLimiters();
    }

    private function registerRateLimiters(): void
    {
        RateLimiter::for('api', fn (Request $request) => $this->limitPerMinute(
            attempts: 120,
            request: $request,
            message: 'rate_limit.api',
        ));

        RateLimiter::for('mobile_login', fn (Request $request) => $this->limitPerMinute(
            attempts: 5,
            request: $request,
            message: 'rate_limit.mobile_login',
            inputs: ['phone'],
        ));

        RateLimiter::for('system_login', fn (Request $request) => $this->limitPerMinute(
            attempts: 5,
            request: $request,
            message: 'rate_limit.system_login',
            inputs: ['email'],
        ));

        RateLimiter::for('registration', fn (Request $request) => $this->limitPerMinute(
            attempts: 3,
            request: $request,
            message: 'rate_limit.registration',
            inputs: ['phone', 'email'],
        ));

        RateLimiter::for('verify_otp', fn (Request $request) => $this->limitPerMinute(
            attempts: 5,
            request: $request,
            message: 'rate_limit.verify_otp',
            inputs: ['phone'],
        ));

        RateLimiter::for('forgot_password', fn (Request $request) => $this->limitPerMinutes(
            attempts: 3,
            minutes: 15,
            request: $request,
            message: 'rate_limit.forgot_password',
            inputs: ['phone'],
        ));

        RateLimiter::for('volunteer-application', fn (Request $request) => $this->limitPerHour(
            attempts: 3,
            request: $request,
            message: 'rate_limit.volunteer_application',
        ));

        RateLimiter::for('password_reset', fn (Request $request) => $this->limitPerMinutes(
            attempts: 5,
            minutes: 15,
            request: $request,
            message: 'rate_limit.password_reset',
            inputs: ['phone'],
        ));

        RateLimiter::for('profile_update', fn (Request $request) => $this->limitPerMinute(
            attempts: 20,
            request: $request,
            message: 'rate_limit.profile_update',
        ));

        RateLimiter::for('password_update', fn (Request $request) => $this->limitPerMinutes(
            attempts: 5,
            minutes: 15,
            request: $request,
            message: 'rate_limit.password_update',
        ));

        RateLimiter::for('phone_update_request', fn (Request $request) => $this->limitPerHour(
            attempts: 2,
            request: $request,
            message: 'rate_limit.phone_update_request',
        ));

        RateLimiter::for('report', fn (Request $request) => $this->limitPerHour(
            attempts: 5,
            request: $request,
            message: 'rate_limit.report',
        ));

        RateLimiter::for('review', fn (Request $request) => $this->limitPerHour(
            attempts: 5,
            request: $request,
            message: 'rate_limit.review',
        ));

        RateLimiter::for('lead', fn (Request $request) => $this->limitPerHour(
            attempts: 5,
            request: $request,
            message: 'rate_limit.lead',
        ));

        RateLimiter::for('booth_request', fn (Request $request) => $this->limitPerHour(
            attempts: 3,
            request: $request,
            message: 'rate_limit.booth_request',
        ));

        RateLimiter::for('event_request', fn (Request $request) => $this->limitPerHour(
            attempts: 3,
            request: $request,
            message: 'rate_limit.event_request',
        ));

        RateLimiter::for('volunteer-application', fn (Request $request) => $this->limitPerHour(
            attempts: 5,
            request: $request,
            message: 'rate_limit.volunteer_application',
        ));
    }

    private function limitPerMinute(int $attempts, Request $request, string $message, array $inputs = []): Limit
    {
        return Limit::perMinute($attempts)
            ->by($this->rateLimitKey($request, $inputs))
            ->response(fn () => errorResponse(__($message), [], 429));
    }

    private function limitPerMinutes(int $attempts, int $minutes, Request $request, string $message, array $inputs = []): Limit
    {
        return Limit::perMinutes($minutes, $attempts)
            ->by($this->rateLimitKey($request, $inputs))
            ->response(fn () => errorResponse(__($message), [], 429));
    }

    private function limitPerHour(int $attempts, Request $request, string $message, array $inputs = []): Limit
    {
        return Limit::perHour($attempts)
            ->by($this->rateLimitKey($request, $inputs))
            ->response(fn () => errorResponse(__($message), [], 429));
    }

    private function rateLimitKey(Request $request, array $inputs = []): string
    {
        $identity = collect($inputs)
            ->map(fn (string $input) => $request->input($input))
            ->first(fn (mixed $value) => filled($value));
        $identity ??= $request->user('mobile')?->getAuthIdentifier();
        $identity ??= $request->user('system')?->getAuthIdentifier();
        $identity ??= 'guest';

        return hash('sha256', implode('|', [(string) $identity, $request->ip()]));
    }
}

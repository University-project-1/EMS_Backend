<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetVolunteerLocale
{
    private const SUPPORTED_LOCALES = ['ar', 'en'];

    public function __construct(private readonly Application $app) {}

    /**
     * Apply the visitor's explicit saved preference before validation and view rendering.
     *
     * @param  Closure(Request): Response  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $this->app->setLocale($this->resolveLocale($request));

        return $next($request);
    }

    private function resolveLocale(Request $request): string
    {
        $preferredLocale = $request->cookie('volunteer_locale');

        if (in_array($preferredLocale, self::SUPPORTED_LOCALES, true)) {
            return $preferredLocale;
        }

        return $request->getPreferredLanguage(self::SUPPORTED_LOCALES)
            ?? config('app.locale');
    }
}

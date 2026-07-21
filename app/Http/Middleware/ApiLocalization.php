<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ApiLocalization
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (in_array($request->header('Accept-Language'), ['ar', 'en'])) {
            $locale = $request->header('Accept-Language') ?? 'en';
            app()->setLocale($locale);
        }
        return $next($request);
    }
}

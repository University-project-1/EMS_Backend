<?php

namespace App\Http\Middleware;

use App\Enum\SystemUserType;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsExhibitor
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! $request->user('system') || $request->user('system')->type !== SystemUserType::EXHIBITOR) {
            return errorResponse(
                message:  __('auth.forbidden'),
                code:  403,
            );
        }

        return $next($request);
    }
}

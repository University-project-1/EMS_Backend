<?php

namespace App\Http\Middleware;

use App\Enum\SystemUserType;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsAdmin
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! $request->user('system') || $request->user('system')->type !== SystemUserType::ADMIN) {
            return errorResponse(
                message:  'Forbidden: Admin access required.',
                code:  403,
            );
        }

        return $next($request);
    }
}

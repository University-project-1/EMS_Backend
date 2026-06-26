<?php

use App\Http\Middleware\ApiLocalization;
use App\Http\Middleware\EnsureUserIsAdmin;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Exceptions\ThrottleRequestsException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias(['type.admin' => EnsureUserIsAdmin::class]);
        $middleware->api([
            'ApiLocalization' => ApiLocalization::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {

        $exceptions->render(function (\Throwable $e, $request) {

            if (! $request->expectsJson()) {
                return null;
            }

            // 1. Validation
            if ($e instanceof ValidationException) {
                return errorResponse(
                    __('validation.failed'),
                    $e->errors(),
                    422
                );
            }

            // 2. Auth
            if ($e instanceof AuthenticationException) {
                return errorResponse(
                    __('auth.unauthenticated'),
                    null,
                    401
                );
            }

            // 3. Authorization
            if ($e instanceof AuthorizationException) {
                return errorResponse(
                    __('auth.forbidden'),
                    null,
                    403
                );
            }

            // 4. Not Found
            if ($e instanceof ModelNotFoundException || $e instanceof NotFoundHttpException) {
                return errorResponse(
                    __('errors.not_found'),
                    null,
                    404
                );
            }

            // 5. Method Not Allowed
            if ($e instanceof MethodNotAllowedHttpException) {
                return errorResponse(
                    __('errors.method_not_allowed'),
                    null,
                    405
                );
            }

            // if ($e instanceof HttpResponseException) {
            //     return $e->getResponse();
            // }

            // 6. Rate limit
            if ($e instanceof ThrottleRequestsException) {
                return errorResponse(
                    __('errors.too_many_requests'),
                    null,
                    429
                );
            }

            if ($e instanceof HttpResponseException) {
                return errorResponse(
                    $e->getMessage() ?: __('errors.http_error'),
                    null,
                    $e->getStatusCode()
                );
            }

            // 7. HTTP exceptions
            if ($e instanceof HttpException) {
                return errorResponse(
                    $e->getMessage() ?: __('errors.http_error'),
                    null,
                    $e->getStatusCode()
                );
            }

            // 8. fallback
            return errorResponse(
                config('app.debug')
                    ? $e->getMessage()
                    : __('errors.server_error'),
                null,
                500
            );
        });

    })->create();

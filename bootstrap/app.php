<?php

use App\Http\Middleware\SetLocale;
use App\Http\Middleware\SuperAdminAuth;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\QueryException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->web(append: [
            SetLocale::class,
        ]);
        $middleware->alias([
            'superadmin.auth' => SuperAdminAuth::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->render(function (ValidationException $e, $request) {
            if ($request->is('api/*') || $request->expectsJson()) {
                return response()->json([
                    'message' => $e->getMessage(),
                    'errors'  => $e->errors(),
                ], 422);
            }
        });

        $exceptions->render(function (AuthenticationException $e, $request) {
            if ($request->is('api/*') || $request->expectsJson()) {
                return response()->json([
                    'message' => 'Unauthenticated.',
                ], 401);
            }
        });

        $exceptions->render(function (AuthorizationException $e, $request) {
            if ($request->is('api/*') || $request->expectsJson()) {
                return response()->json([
                    'message' => 'This action is unauthorized.',
                ], 403);
            }
        });

        $exceptions->render(function (AccessDeniedHttpException $e, $request) {
            if ($request->is('api/*') || $request->expectsJson()) {
                return response()->json([
                    'message' => 'This action is unauthorized.',
                ], 403);
            }
        });

        $exceptions->render(function (ModelNotFoundException $e, $request) {
            if ($request->is('api/*') || $request->expectsJson()) {
                return response()->json([
                    'message' => 'Resource not found.',
                ], 404);
            }
        });

        $exceptions->render(function (NotFoundHttpException $e, $request) {
            if ($request->is('api/*') || $request->expectsJson()) {
                return response()->json([
                    'message' => 'The requested resource was not found.',
                ], 404);
            }
        });

        $exceptions->render(function (QueryException $e, $request) {
            $isMissingTable = $e->getCode() === '42S02'
                || str_contains($e->getMessage(), 'Base table or view not found');

            if ($isMissingTable && $request->is('app/*') && ! $request->expectsJson()) {
                return redirect('/app/dashboard')
                    ->with('error', 'Some module tables are not initialized in this environment yet.');
            }

            if ($isMissingTable && ($request->is('api/*') || $request->expectsJson())) {
                return response()->json([
                    'message' => 'Some module tables are not initialized in this environment yet.',
                ], 503);
            }
        });

        $exceptions->render(function (Throwable $e, $request) {
            if ($request->is('api/*') || $request->expectsJson()) {
                $statusCode = method_exists($e, 'getStatusCode') ? $e->getStatusCode() : 500;

                if ($statusCode === 500) {
                    return response()->json([
                        'message' => app()->environment('production')
                            ? 'Server error occurred.'
                            : $e->getMessage(),
                        'exception' => app()->environment('production') ? null : get_class($e),
                        'file'      => app()->environment('production') ? null : $e->getFile(),
                        'line'      => app()->environment('production') ? null : $e->getLine(),
                    ], 500);
                }
            }
        });
    })->create();

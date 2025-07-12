<?php

use App\Enums\ResponseTypes;
use App\Http\Middleware\HandleInertiaRequests;
use App\Http\Services\HelperService;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\TooManyRequestsHttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->web(append: [
            HandleInertiaRequests::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->renderable(function (Exception $e, Request $request) {
            $helper = new HelperService();

            if ($request->expectsJson()) {
                if ($e instanceof AuthenticationException) {
                    return $helper->messageException("Sorry, You aren't authenticated", 401);
                } elseif ($e instanceof TooManyRequestsHttpException) {
                    return $helper->messageException('Too many attempts, please slow down the request.', $e->getStatusCode(), ResponseTypes::SERVER_ERROR);
                } elseif ($e instanceof AccessDeniedHttpException) {
                    return $helper->messageException('Sorry, You are unauthorized...', $e->getStatusCode());
                } elseif ($e instanceof NotFoundHttpException) {
                    return $helper->messageException('Route not found...');
                }
            }
        });
    })->create();

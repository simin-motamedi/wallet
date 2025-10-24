<?php

use App\Exceptions\CodedException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        //
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->render(function (Throwable $e) {
            if ($e instanceof CodedException) {
                return response()->json([
                    'success' => false,
                    'errorType' => $e->getErrorType(),
                    'errorCode' => $e->getErrorCode()->value,
                    'message' => $e->getMessage(),
                ], $e->getCode() ?: 400);
            } else {
                return response()->json([
                    'success' => false,
                    'errorType' => 'server_error',
                    'errorCode' => 500,
                    'message' => $e->getMessage(),
                ], 500);
            }
        });
    })
    ->create();
